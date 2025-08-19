<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsDeployment extends Model
{
    // Optional: specify the table if Laravel can't guess it (here it's fine but explicit is ok)
    protected $table = 'cms_deployments';

    // Allow mass assignment for these columns
    protected $fillable = [
        'server_ip',
        'ssh_user',
        'ssh_private_key',
        'ssh_password',
        'domain',
        'status',
    ];

    // If you want to hide sensitive fields when returning JSON
    protected $hidden = [
        'ssh_private_key',
        'ssh_password',
    ];
}
