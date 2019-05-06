<?php namespace Ariol\Admin\Middleware;

use Closure;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param $roles
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roles = explode('|', $roles);

        if (! $request->user() || ($request->user() && !$request->user()->hasRole($roles))) {
            abort(403);
        }

        return $next($request);
    }
}
