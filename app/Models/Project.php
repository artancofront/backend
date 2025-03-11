<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     title="Project",
 *     description="Project model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="E-commerce CMS"),
 *     @OA\Property(property="slug", type="string", example="ecommerce-cms"),
 *     @OA\Property(property="description", type="string", nullable=true, example="A fully functional e-commerce CMS."),
 *     @OA\Property(property="basic_info", type="object", nullable=true, example={"type": "CMS", "version": "1.0.0"}),
 *     @OA\Property(property="super_admin_username", type="string", nullable=true, example="admin"),
 *     @OA\Property(property="super_admin_password", type="string", nullable=true, example="hashed_password"),
 *     @OA\Property(property="theme_settings", type="object", nullable=true, example={"theme": "dark", "font": "Roboto"}),
 *     @OA\Property(property="cms_settings", type="object", nullable=true, example={"multi_language": true, "cache_enabled": false}),
 *     @OA\Property(property="plugins", type="object", nullable=true, example={"seo_plugin": true, "analytics": true}),
 *     @OA\Property(property="features", type="object", nullable=true, example={"search": true, "notifications": true}),
 *     @OA\Property(property="status", type="string", enum={"in_progress", "completed", "paused", "canceled"}, example="in_progress"),
 *     @OA\Property(property="client_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Urgent project"),
 *     @OA\Property(property="billing_info", type="string", nullable=true, example="Paid in full"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="deployment_date", type="string", format="date-time", nullable=true, example="2025-05-10T12:30:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */
class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'basic_info',
        'super_admin_username',
        'super_admin_password',
        'theme_settings',
        'cms_settings',
        'plugins',
        'features',
        'status',
        'client_id',
        'notes',
        'billing_info',
        'is_active',
        'deployment_date',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array<string, string>
     */
    protected $casts = [
        'basic_info' => 'array',
        'theme_settings' => 'array',
        'cms_settings' => 'array',
        'plugins' => 'array',
        'features' => 'array',
        'status' => 'string',
        'is_active' => 'boolean',
        'deployment_date' => 'datetime',
    ];

    /**
     * Get the client that owns the project.
     *
     * @return BelongsTo
     *
     * @OA\Property(
     *     property="client",
     *     ref="#/components/schemas/Client"
     * )
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
