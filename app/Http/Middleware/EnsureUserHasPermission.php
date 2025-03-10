<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $resource
     * @param  string  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $resource, $action)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if user has permissions for the resource
        if (!isset($user->permissions[$resource])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if the required action is within the permissions array for the resource
        if (!in_array($action, $user->permissions[$resource])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

