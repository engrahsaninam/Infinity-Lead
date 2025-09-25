<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCampaignJob;
use App\Models\Campaign;
use Cache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CampaignCommand extends Command
{
    protected $signature = 'run:campaigns';
    protected $description = 'Dispatch eligible campaigns';

    public function handle()
    {
        $now = Carbon::now();
        $currentDay = strtolower($now->format('D'));

        $campaigns = Campaign::where('status', Campaign::STATUS_LAUNCHED)
            ->whereRaw("FIND_IN_SET(?, days)", [$currentDay])
            ->cursor();

        $jobs = [];

        foreach ($campaigns as $campaign) {


           


            $senderAccount = Campaign::selectEmailAccount($campaign);
            if (!$senderAccount) {
                Log::warning("Campaign {$campaign->id} skipped: No eligible email account.");
                continue;
            }


            $tzNow = $now->copy()->setTimezone($campaign->timezone);
            $start = Carbon::createFromFormat('H:i:s', $campaign->start, $campaign->timezone);
            $end = Carbon::createFromFormat('H:i:s', $campaign->end, $campaign->timezone);

            if (!$tzNow->between($start, $end)) {
                Log::info("Campaign {$campaign->id} skipped: Outside allowed time window.");
                continue;
            }

            if (!$campaign->analytics()->whereNull('sent_at')->exists()) {
                Log::info("Campaign {$campaign->id} skipped: No pending rows to send.");
                continue;
            }

            $lock = Cache::lock("campaign_{$campaign->id}_lock", 300);

            try {
                if ($lock->get()) {
                    // SAFELY build job and set connection
                    $job = new ProcessCampaignJob($campaign->id);
                    //$job->onConnection('octane');
                    $jobs[] = $job;
                } else {
                    Log::info("Campaign {$campaign->id} skipped: Unable to acquire lock in time.");
                }
            } catch (\Throwable $e) {
                Log::error("Campaign {$campaign->id} lock error: " . $e->getMessage());
            } finally {
                optional($lock)->release();
            }
        }

        if (!empty($jobs)) {
            Bus::batch($jobs)->dispatch();
            Log::info('Dispatched ' . count($jobs) . ' campaign jobs.');
        } else {
            Log::info('No campaigns to dispatch.');
        }
    }
}
