<?php

namespace App\Jobs;


use App\Models\Campaign;
use App\Models\EmailAccount;
use App\Models\Record;
use App\Services\CampaignRunService;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;


class ProcessCampaignJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;
    use Batchable;

    public $timeout = 300;
    public $failOnTimeout = true;
    public $tries = 1;
    public $maxExceptions = 1;
    public $campaignId;
    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }
    public function handle(): void
    {
        $campaign = Campaign::findOrFail($this->campaignId);

        $analyticsQuery = $campaign->analytics()
            ->whereNull('sent_at')
            ->where('skipped', 0)
            ->where('blacklisted', 0)
            ->orderBy('id');

        $analyticsQuery->chunkById(50, function ($rows) use ($campaign) {
            $rowIds = $rows->pluck('id')->toArray();
            if (Campaign::__RUN_PRODUCTION__) {
                //ProcessCampaignChunkJob::dispatch($campaign->id, $rowIds)->onConnection('octane');
                ProcessCampaignChunkJob::dispatch($campaign->id, $rowIds);
            } else {
                $job = new ProcessCampaignChunkJob($campaign->id, $rowIds);
                $service = new CampaignService();
                $job->handle($service);
            }
        });
    }
}
