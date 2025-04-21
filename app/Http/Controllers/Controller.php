<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\OpenApi(
 *      security={{ "BearerAuth": {} }}
 *  )
 * @OA\Info(title="CMS manager", version="0.1")
 * @OA\Server(
 *      url="http://localhost/cms-template/public",
 *      description="Local development server"
 *  )
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Authentication using Bearer token (Sanctum)"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="cookieSession",
 *     type="apiKey",
 *     in="cookie",
 *     name="laravel_session",
 *     description="Authentication using session cookie (laravel_session)"
 * )
 * @OA\Security(
 *     securityScheme="cookieSession"
 * )
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="to", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=150)
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
