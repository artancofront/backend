<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     title="Client",
 *     description="Client model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone_number", type="string", example="+1234567890"),
 *     @OA\Property(property="company_name", type="string", nullable=true, example="Acme Inc."),
 *     @OA\Property(property="address", type="string", nullable=true, example="123 Street, City, Country"),
 *     @OA\Property(property="website_url", type="string", nullable=true, example="https://example.com"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "on_hold"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */
class Client extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'company_name',
        'address',
        'website_url',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the projects associated with the client.
     *
     * @return HasMany
     *
     * @OA\Property(
     *     property="projects",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Project")
     * )
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
