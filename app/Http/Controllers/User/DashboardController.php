<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lists;
use App\Models\Subscriber;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use CommonTraits;
    public function quota(Request $request)
    {
        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        if (!$activeSubscription || !$activeSubscription->plan) {
            return $this->sendError('No active subscription found.');
        }

        $plan = $activeSubscription->plan;
        $start = $activeSubscription->start;
        $end = $activeSubscription->end;

        $listsUsed = Lists::where('user_id', $user->id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->count();

        $listIds = Lists::where('user_id', $user->id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->pluck('id');

        $subscriberUsed = Subscriber::whereIn('list_id', $listIds)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->count();

        $campaignUsed = Campaign::where('user_id', $user->id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->count();

            $options=$plan['options'];
        $data = [
            'list_max' => $options['list_max'] ?? 0,
            'lists_used' => $listsUsed,
            'subscriber_max' => $options['subscriber_max'] ?? 0,
            'subscriber_used' => $subscriberUsed,
            'campaign_max' => $options['campaign_max'] ?? 0,
            'campaign_used' => $campaignUsed,
            'credits' => $activeSubscription->credits,
            'credits_used' => 0,
            'start' => date('Y-m-d H:i', strtotime($start)),
            'end' => date('Y-m-d H:i', strtotime($end)),
        ];

        return $this->sendSuccess('Quota fetched successfully', $data);
    }

}
