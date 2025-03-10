<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Annotations as OA;

class GenerateSwaggerDocs extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Generate Swagger API documentation';

    public function handle()
    {
        $swagger = \OpenApi\scan(app_path()); // Scans your app directory for annotations
        file_put_contents(public_path('swagger.json'), $swagger->toJson());
        $this->info('Swagger docs generated successfully!');
    }
}
