<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        // check for user rights
        if ($user = Auth::user()) {

            $total_all_sidebar = $user->managedAssets()->count();
            view()->share('total_all_sidebar', $total_all_sidebar);


            $total_deployed_sidebar = $user->managedAssets()->Deployed()->count();
            view()->share('total_deployed_sidebar', $total_deployed_sidebar);

            $total_rtd_sidebar = $user->managedAssets()->RTD()->count();
            view()->share('total_rtd_sidebar', $total_rtd_sidebar);

            $total_pending_sidebar = $user->managedAssets()->Pending()->count();
            view()->share('total_pending_sidebar', $total_pending_sidebar);

            $total_undeployable_sidebar = $user->managedAssets()->Undeployable()->count();
            view()->share('total_undeployable_sidebar', $total_undeployable_sidebar);

            $total_archived_sidebar = $user->managedAssets()->Archived()->count();
            view()->share('total_archived_sidebar', $total_archived_sidebar);

            $total_requestable_sidebar = $user->managedAssets()->RequestableAssets()->count();
            view()->share('total_requestable_sidebar', $total_requestable_sidebar);

            $total_dueforaudit_sidebar = $user->managedAssets()->DueForAudit(Setting::getSettings())->count();
            view()->share('total_dueforaudit_sidebar', $total_dueforaudit_sidebar);

            $total_overdueforaudit_sidebar = $user->managedAssets()->OverdueForAudit()->count();
            view()->share('total_overdueforaudit_sidebar', $total_overdueforaudit_sidebar);

            $total_people_sidebar = $user->managedUsers()->count();
            view()->share('total_people_sidebar', $total_people_sidebar);

            $total_departments_sidebar = $user->managedDepartments()->count();
            view()->share('total_departments_sidebar', $total_departments_sidebar);

            $total_locations_sidebar = $user->managedLocations()->count();
            view()->share('total_locations_sidebar', $total_locations_sidebar);
        }

        return $next($request);
    }
}
