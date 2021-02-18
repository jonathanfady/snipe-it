<?php

namespace App\Http\Middleware;

use App\Models\Asset;
use App\Models\Setting;
use Closure;

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
        try {
            $total_deployed_sidebar = Asset::Deployed()->count();
            view()->share('total_deployed_sidebar', $total_deployed_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_rtd_sidebar = Asset::RTD()->count();
            view()->share('total_rtd_sidebar', $total_rtd_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_pending_sidebar = Asset::Pending()->count();
            view()->share('total_pending_sidebar', $total_pending_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_undeployable_sidebar = Asset::Undeployable()->count();
            view()->share('total_undeployable_sidebar', $total_undeployable_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_archived_sidebar = Asset::Archived()->count();
            view()->share('total_archived_sidebar', $total_archived_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_requestable_sidebar = Asset::RequestableAssets()->count();
            view()->share('total_requestable_sidebar', $total_requestable_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_dueforaudit_sidebar = Asset::DueForAudit(Setting::getSettings())->count();
            view()->share('total_dueforaudit_sidebar', $total_dueforaudit_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        try {
            $total_overdueforaudit_sidebar = Asset::OverdueForAudit()->count();
            view()->share('total_overdueforaudit_sidebar', $total_overdueforaudit_sidebar);
        } catch (\Exception $e) {
            \Log::debug($e);
        }

        return $next($request);
    }
}
