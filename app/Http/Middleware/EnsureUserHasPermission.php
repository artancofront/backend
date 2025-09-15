<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $resource  The category of the permission (e.g., 'users')
     * @param  string  $action  The specific action (e.g., 'create')
     */
    public function handle(Request $request, Closure $next,string $resource,string $action)
    {
        // Get the authenticated user
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Assuming the user has a 'role_id' to relate to a role
        $role = $user->role;

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found for the user to check permissions on this resource'
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if the user has the permission for the given resource and action
        if (!$role->hasPermission($resource, $action)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action on the resource.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Proceed to the next request if permission is granted
        return $next($request);
    }
}

