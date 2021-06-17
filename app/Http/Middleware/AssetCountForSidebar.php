<?php

namespace App\Http\Middleware;

use App\Models\Asset;
use App\Models\Setting;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetCountForSidebar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $assets = resolve(Asset::class);
        // check for user rights
        if ($user = Auth::user()) {
            if (!$user->isSuperUser() && !$user->isAdmin()) {
                // limit view for non-admin users
                $assets = Asset::where('assets.focal_point_id', '=', $user->id);
            }
        }

        try {
            $total_deployed_sidebar = $assets->Deployed()->count();
            view()->share('total_deployed_sidebar', $total_deployed_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_rtd_sidebar = $assets->RTD()->count();
            view()->share('total_rtd_sidebar', $total_rtd_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_pending_sidebar = $assets->Pending()->count();
            view()->share('total_pending_sidebar', $total_pending_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_undeployable_sidebar = $assets->Undeployable()->count();
            view()->share('total_undeployable_sidebar', $total_undeployable_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_archived_sidebar = $assets->Archived()->count();
            view()->share('total_archived_sidebar', $total_archived_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_requestable_sidebar = $assets->RequestableAssets()->count();
            view()->share('total_requestable_sidebar', $total_requestable_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_dueforaudit_sidebar = $assets->DueForAudit(Setting::getSettings())->count();
            view()->share('total_dueforaudit_sidebar', $total_dueforaudit_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_overdueforaudit_sidebar = $assets->OverdueForAudit()->count();
            view()->share('total_overdueforaudit_sidebar', $total_overdueforaudit_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        return $next($request);
    }
}
