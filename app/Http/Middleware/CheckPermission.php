<?php

namespace App\Http\Middleware;

use App\Feature;
use App\Permission;
use Closure;
use Route;
use App;
use Cache;

class CheckPermission
{
    protected $except = [
        'home',
        'auth.login',
        'auth.logout',
        'auth.password',
        'dashboard.show',
        'dashboard.welcome',
        'dashboard.bootstrap',
        'dashboard.agreement',
        'user.phone.vcode',
        'user.phone.bind',
        'user.phone.unbind',
        'system.datetime',
        'auth.password.reset',
        //'statistics.test'
    ];

    public function handle($request, Closure $next)
    {
        $current_route = Route::currentRouteName();
        $role = auth()->user()->role;

        if (Cache::has('permission.role.' . $role->id)) {
            $features = Cache::get('permission.role.' . $role->id);
        } else {
            $feature_ids = Permission::where('role_id', $role->id)->lists('feature_id');
            $features = Feature::whereIn('id', $feature_ids->toArray())->lists('route');
            $features = $features->toArray();
            Cache::forever('permission.role.' . $role->id, $features);
        }
        
        $allowed_routes = array_merge($this->except, $features);
        if ( ! in_array($current_route, $allowed_routes)) {
            App::abort(403, 'Permission denied');
        }

        return $next($request);
    }
}
