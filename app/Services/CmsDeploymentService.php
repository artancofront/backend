<?php

namespace App\Services;

use App\Models\CmsDeployment;
use App\Repositories\CmsDeploymentRepository;
use phpseclib3\Net\SSH2;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class CmsDeploymentService
{
    protected $repo;

    public function __construct(CmsDeploymentRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Create an authenticated SSH2 connection.
     */
    protected function ssh(CmsDeployment $deployment): SSH2
    {
        $ssh = new SSH2($deployment->server_ip);

        if ($deployment->ssh_private_key) {
            $key = PublicKeyLoader::load($deployment->ssh_private_key);
            if (!$ssh->login($deployment->ssh_user, $key)) {
                throw new \Exception('SSH login failed using private key');
            }
        } elseif ($deployment->ssh_password) {
            if (!$ssh->login($deployment->ssh_user, $deployment->ssh_password)) {
                throw new \Exception('SSH login failed using password');
            }
        } else {
            throw new \Exception('No SSH authentication method provided');
        }

        return $ssh;
    }

    /**
     * Create an authenticated SFTP connection.
     */
    protected function sftp(CmsDeployment $deployment): SFTP
    {
        $sftp = new SFTP($deployment->server_ip);

        if ($deployment->ssh_private_key) {
            $key = PublicKeyLoader::load($deployment->ssh_private_key);
            if (!$sftp->login($deployment->ssh_user, $key)) {
                throw new \Exception('SFTP login failed using private key');
            }
        } elseif ($deployment->ssh_password) {
            if (!$sftp->login($deployment->ssh_user, $deployment->ssh_password)) {
                throw new \Exception('SFTP login failed using password');
            }
        } else {
            throw new \Exception('No SFTP authentication method provided');
        }

        return $sftp;
    }

    /**
     * Step 1: Install required packages on server.
     */
    public function installRequirements(CmsDeployment $deployment)
    {
        $ssh = $this->ssh($deployment);

        $commands = [
            'sudo apt update',
            'sudo apt install -y nginx mysql-server redis-server php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip unzip',
            'curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -',
            'sudo apt install -y nodejs',
            'sudo npm install -g pm2 composer'
        ];

        foreach ($commands as $cmd) {
            $ssh->exec($cmd);
        }

        $this->repo->update($deployment->id, ['status' => 'requirements_installed']);
    }

    /**
     * Step 2: Upload Laravel API archive and extract it.
     */
    public function uploadLaravelApi(CmsDeployment $deployment, string $localTarPath)
    {
        $sftp = $this->sftp($deployment);

        $remotePath = '/var/www/laravel-api.tar.gz';
        $sftp->put($remotePath, file_get_contents($localTarPath));

        $ssh = $this->ssh($deployment);
        $ssh->exec("sudo mkdir -p /var/www/laravel-api");
        $ssh->exec("sudo tar -xzf $remotePath -C /var/www/laravel-api --strip-components=1");
        $ssh->exec("sudo chown -R www-data:www-data /var/www/laravel-api");

        $this->repo->update($deployment->id, ['status' => 'api_uploaded']);
    }

    /**
     * Step 3: Install Laravel dependencies & migrate DB.
     */
    public function setupLaravel(CmsDeployment $deployment)
    {
        $ssh = $this->ssh($deployment);

        $ssh->exec("cd /var/www/laravel-api && composer install --no-dev --optimize-autoloader");
        $ssh->exec("php /var/www/laravel-api/artisan migrate --force");
        $ssh->exec("php /var/www/laravel-api/artisan db:seed --force");

        $this->repo->update($deployment->id, ['status' => 'laravel_ready']);
    }

    /**
     * Step 4: Configure Nginx to serve Laravel API & Next.js frontend.
     */
    public function setupNginx(CmsDeployment $deployment)
    {
        $ssh = $this->ssh($deployment);

        $nginxConfig = <<<NGINX
server {
    listen 80;
    server_name {$deployment->domain};

    root /var/www/frontend;
    index index.html;

    location / {
        try_files \$uri /index.html;
    }

    location /api {
        root /var/www/laravel-api/public;
        index index.php index.html;
        try_files \$uri \$uri/ /index.php?\$query_string;

        location ~ \.php\$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
        }
    }
}
NGINX;

        $ssh->exec("echo '$nginxConfig' | sudo tee /etc/nginx/sites-available/{$deployment->domain}.conf");
        $ssh->exec("sudo ln -sf /etc/nginx/sites-available/{$deployment->domain}.conf /etc/nginx/sites-enabled/");
        $ssh->exec("sudo nginx -t && sudo systemctl reload nginx");

        $this->repo->update($deployment->id, ['status' => 'nginx_configured']);
    }
}
