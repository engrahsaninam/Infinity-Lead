<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use App\Models\Campaign;
use App\Models\Infinity;
use App\Models\Lists;
use App\Models\Mapping;
use App\Models\Tag;
use App\Models\Template;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    //
    use CommonTraits;
    public function show(Request $request)
    {
        $campaign = Campaign::with(['list','template'])->find($request->id);
        return $this->sendSuccess('Campaign fetched successfully!', $campaign);
    }
    public function count()
    {
        $count = Campaign::where('user_id', Auth::id())->count();
        return $this->sendSuccess('Campaigns count!', $count);
    }
    public function edit(Request $request)
    {
        $campaign = Campaign::with(['template','list'])->find($request->id);
        if ($campaign->status === Campaign::STATUS_COMPLETED) {
            return $this->sendError('Campaign can not be edited!', 421);
        }
        return $this->sendSuccess('Campaign fetched successfully!', $campaign);
    }
    public function fetch(Request $request)
    {
        $search = $request->search;
        $campaign = Campaign::where('user_id', Auth::id());
        $campaign->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name'], "like", "%$search%");
            });
        });
        $campaign = $campaign->paginate($request->records);
        return response()->json($campaign);
    }
    public function name(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        if (!$request->id) {
            $request->merge(['user_id' => auth()->id()]);
            $campaign = Campaign::create($request->all());
            return $this->sendSuccess('Campaign created successfully!', $campaign);
        } else {
            $campaign = Campaign::find($request->id);
            $campaign->update([
                'name' => $request->name
            ]);
            return $this->sendSuccess('Campaign updated successfully!', $campaign);
        }
    }
    public function accounts(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
            'accounts' => 'array',
            'accounts.*' => 'exists:email_accounts,id'
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        if ($request->id) {
            $campaign = Campaign::find($request->id);
            if ($request->has('accounts')) {
                $campaign->emailAccounts()->sync($request->accounts);
            }
            return $this->sendSuccess('Accounts added successfully!');
        }

        return response()->json(['error' => 'Campaign already exists'], 421);
    }
    public function controls(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'controls' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        if ($request->id) {
            $campaign = Campaign::find($request->id)->update($request->all());
            return $this->sendSuccess('Campaign controls saved!', $campaign);
        }
        return $this->sendError('Campaign already exists', 421);
    }
    public function list(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'list_id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $list=Lists::find($request->list_id);
        if(!$list->csv){
            return $this->sendError('List has no subscribers!',421);
        }
        if ($request->id){
            $campaign=Campaign::find($request->id)
            ->update([
                'list_id'=>$request->list_id
            ]);
            return  $this->sendSuccess('Campaign list attached!',$campaign);
        }
        return $this->sendError('Campaign already exists', 421);
    }
    public function lists(Request $request){
        $lists=Lists::where('user_id',Auth::id())->select('id','name','created_at')->get();
        return $this->sendSuccess('Lists fetched successfully!', $lists);
    }
    public function templates(Request $request)
    {
        $templates = Template::where('user_id', Auth::id())->select('id', 'name', 'created_at')->get();
        return $this->sendSuccess('Templates fetched successfully!', $templates);
    }

    public function days(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'days' => 'required|array',
        ]);

        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $campaign = Campaign::find($request->id);
        $campaign->update(['days' => $request->days]);
        return $this->sendSuccess('Days updated successfully!');
    }
    public function timezone(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'timezone' => 'required',
        ]);

        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $campaign = Campaign::find($request->id);
        $campaign->update(['timezone' => $request->timezone]);
        return $this->sendSuccess('Timezone updated successfully!');
    }
    public function time(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'day' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $dayPresets = [
            'full' => ['start' => '08:00 AM', 'end' => '06:00 PM'],
            'morning' => ['start' => '08:00 AM', 'end' => '11:45 AM'],
            'afternoon' => ['start' => '01:00 PM', 'end' => '04:45 PM'],
        ];
        $dayType = $request->day;
        if ($dayType !== 'custom' && isset($dayPresets[$dayType])) {
            $start = date('H:i:s', strtotime($dayPresets[$dayType]['start']));
            $end = date('H:i:s', strtotime($dayPresets[$dayType]['end']));
        } else {
            $start = date('H:i:s', strtotime($request->start));
            $end = date('H:i:s', strtotime($request->end));
        }
        $campaign = Campaign::find($request->id);
        $campaign->update([
            'day' => $dayType,
            'start' => $start,
            'end' => $end,
        ]);
        return $this->sendSuccess('Time updated successfully!');
    }
    public function deliverability(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'deliverability' => 'required',
        ]);

        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $campaign = Campaign::find($request->id);
        $campaign->update(['deliverability' => $request->deliverability]);
        return $this->sendSuccess('Deliverability updated successfully!');
    }


    public function subject(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',  // Ensure campaign ID exists in the database
            'subject' => 'required|string',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        Campaign::find($request->id)->update([
            'subject' => $request->subject,
        ]);
        return response()->json(['message' => 'Subject saved successfully!']);
    }
    public function saveMessage(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
            'message' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        Campaign::find($request->id)->update([
            'message' => $request->message,
        ]);
        return response()->json(['message' => 'Message saved successfully!']);
    }
    public function emailContent(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
            'subject' => 'required|string',
            'template_id' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }

        foreach ($request->followups as $followup) {
            if (empty($followup['days'])) {
                return response()->json('Days are required for each followup.', 421);
            }
            if (empty($followup['message'])) {
                return response()->json('Message is required for each followup.', 421);
            }
        }


        $campaign=Campaign::find($request->id);

        $template = Template::find($request->template_id);
        $tags = Tag::where('user_id', Auth::id())->pluck('name','id')->toArray();
        $usedTags = [];
        foreach ($tags as $id=>$tag) {
            if (Str::contains($template->body, '{' . $tag . '}')) {
                $usedTags[$id] = $tag;
            }
        }
        foreach($usedTags as $id=>$uTag){
            $mapping=Mapping::where('tag_id',$id)->where('list_id',$campaign->list_id)->count();
            if($mapping===0){
                return $this->sendError("{$uTag} mapping against list [{$campaign->list->name}] is missing!",421);
            }
        }
        $campaign->update([
            'subject' => $request->subject,
            'template_id' => $request->template_id,
        ]);
        // foreach ($request->followups as $followup) {
        //     Followup::updateOrCreate(
        //         ['id' => $followup['id']],
        //         [
        //             'campaign_id' => $request->id,
        //             'days' => $followup['days'],
        //             'message' => $followup['message'],
        //         ]
        //     );
        // }





        





        return response()->json(['message' => 'Email set successfully!']);
    }
    public function launch(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
            'controls' => 'required',
            'accounts' => 'array',
            'accounts.*' => 'exists:email_accounts,id',
            'days' => 'required|array',
            'timezone' => 'required',
            'deliverability' => 'required',
            'day' => 'required',
            'start' => 'required',
            'end' => 'required',
            'subject' => 'required|string',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        // foreach ($request->followups as $followup) {
        //     if (empty($followup['days'])) {
        //         return response()->json('Days are required for each followup.', 421);
        //     }
        //     if (empty($followup['message'])) {
        //         return response()->json('Message is required for each followup.', 421);
        //     }
        // }
        if ($request->id) {
            $campaign = Campaign::find($request->id);
            $campaign->update($request->all());
            //foreach ($request->followups as $followup) {
                // Followup::updateOrCreate(
                //     ['id' => $followup['id']],
                //     [
                //         'campaign_id' => $request->id,
                //         'days' => $followup['days'],
                //         'message' => $followup['message'],
                //     ]
                // );
            //}
            if ($request->has('accounts')) {
                $campaign->emailAccounts()->sync($request->accounts);
            }
            $chunkSize = 1000;
            $campaign->list->csv->subscribers->chunk($chunkSize)->each(function ($chunk) use ($campaign) {
                $recordsToInsert = [];
                foreach ($chunk as $row) {
                    $recordsToInsert[] = [
                        'campaign_id' => $campaign->id,
                        'subscriber_id' => $row->id,
                    ];
                }
                Analytics::insertOrIgnore($recordsToInsert);
            });
            $campaign->update(['status' => Campaign::STATUS_LAUNCHED]);
            return $this->sendSuccess('Campaign launched successfully!', $campaign);
        }
    }
    public function playPause(Request $request)
    {

        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $campaign = Campaign::find($request->id);
        $campaign->status = $campaign->status === Campaign::STATUS_LAUNCHED ? Campaign::STATUS_PAUSED : Campaign::STATUS_LAUNCHED;
        $campaign->save();
        return $this->sendSuccess('Campaign status updated successfully!', $campaign);
    }

    public function campaigns_download(Request $request){
        $campaign=Campaign::find($request->id);
        $filter=$request->filter;
        $rows = Analytics::with('subscriber')->where('campaign_id', $request->id);
        $filter = $request->filter;
        if ($filter) {
            switch ($filter) {
                case 'contacted':
                    $rows->where('sent', 1);
                    break;
                case 'not_contacted':
                    $rows->where('sent', 0);
                    break;
                case 'replied':
                    $rows->where('sent', 1)->where('replied', 1);
                    break;
                case 'not_replied':
                    $rows->where('sent', 1)->where('replied', 0);
                    break;
                case 'bounced':
                    $rows->where('bounced', 1);
                    break;
                case 'skipped':
                    $rows->where('skipped', 1);
                    break;
                case 'all':
                default:
                    // No additional filter
                    break;
            }
        }
        $rows = $rows->get();
        return view('exports.campaigns',compact('rows','campaign','filter'));
    }
    public function pagination(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }

        $rows = Analytics::with('subscriber')->where('campaign_id', $request->id);

        $filter = $request->filter;
        if ($filter) {
            switch ($filter) {
                case 'contacted':
                    $rows->where('sent', 1);
                    break;
                case 'not_contacted':
                    $rows->where('sent', 0);
                    break;
                case 'replied':
                    $rows->where('sent', 1)->where('replied', 1);
                    break;
                case 'not_replied':
                    $rows->where('sent', 1)->where('replied', 0);
                    break;
                case 'bounced':
                    $rows->where('bounced', 1);
                    break;
                case 'skipped':
                    $rows->where('skipped', 1);
                    break;
                case 'all':
                default:
                    // No additional filter
                    break;
            }
        }

        $paginated = $rows->paginate($request->records);
        return $this->sendSuccess('Analytics fetched successfully!', $paginated);
    }
    public function attachment(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
            'attachment' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $campaign = Campaign::find($request->id);
        $attachment = $request->attachment;
        $campaign->update([
            'attachment' => Infinity::upload($attachment, Infinity::_PUBLIC_CAMP_ATTACH)
        ]);
        return $this->sendSuccess('Attachment uploaded successfully!');

    }
    public function analytics(Request $request)
    {
        $campaign = Campaign::withCount([
            'analytics',
            'analytics as rows_count' => function ($query) {
            },
            'analytics as sent_count' => function ($query) {
                $query->where('sent', 1);
            },
            'analytics as replied_count' => function ($query) {
                $query->where('replied', 1);
            },
            'analytics as bounced_count' => function ($query) {
                $query->where('bounced', 1);
            },
            'analytics as skipped_count' => function ($query) {
                $query->where('skipped', 1);
            },
            'analytics as contacted_count' => function ($query) {
                $query->where('sent', 1);
            },
            'analytics as not_contacted_count' => function ($query) {
                $query->where('sent', 0);
            },
            'analytics as responded_sent_count' => function ($query) {
                $query->where('sent', 1)->where('replied', 1);
            },
            'analytics as not_responded_sent_count' => function ($query) {
                $query->where('sent', 1)->where('replied', 0);
            }
        ])->findOrFail($request->id);

        $total = $campaign->rows_count;
        $sent = $campaign->sent_count;
        $replied = $campaign->replied_count;
        $bounced = $campaign->bounced_count;
        $skipped = $campaign->skipped_count;
        $contacted = $campaign->contacted_count;
        $not_contacted = $campaign->not_contacted_count;
        $responded = $campaign->responded_sent_count;
        $not_responded = $campaign->not_responded_sent_count;

        return response()->json([
            'reply_rate' => $total ? round(($replied / $total) * 100, 2) : 0,
            'bounced_rate' => $total ? round(($bounced / $total) * 100, 2) : 0,
            'progress' => $total ? round(($sent / $total) * 100, 2) : 0,
            'skipped' => $skipped,
            'bounced' => $bounced,
            'replied' => $replied,
            'sent' => $sent,
            'contacted' => $contacted,
            'not_contacted' => $not_contacted,
            'responded' => $responded,
            'not_responded' => $not_responded,
            'all' => $campaign->rows_count,
        ]);
    }
    public function checkReply(Request $request)
    {
        try {
            $campaign = Campaign::findOrFail($request->id);

            // Prepare subscribers list dynamically
            $chunkSize = 500; // performance optimization
            $recordsToInsert = [];
            function isEmail($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            }
            $campaign->list->csv->subscribers->chunk($chunkSize)->each(function ($chunk) use (&$recordsToInsert, $campaign) {
                foreach ($chunk as $row) {
                    $value = trim($row->column_1);
                    $recordsToInsert[] = [
                        'campaign_id'      => $campaign->id,
                        'subscriber_id'    => $row->id,
                        'subscriber_value' => $value, // email ya name dynamically
                        'is_email'         => isEmail($value), // true = email, false = name
                    ];
                }
            });

            if (empty($recordsToInsert)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No subscribers found for this campaign.'
                ], 404);
            }

            // Connect to Gmail IMAP
            $imap = imap_open(
                '{'.env('IMAP_HOST').':'.env('IMAP_PORT').'/imap/'.env('IMAP_ENCRYPTION').'}INBOX',
                env('IMAP_USERNAME'),
                env('IMAP_PASSWORD')
            );

            if (!$imap) {
                throw new \Exception('IMAP connection failed: ' . imap_last_error());
            }

            // Fetch only unread emails
            $emails = imap_search($imap, 'UNSEEN');
            $repliedSubscribers = [];

            if ($emails) {
                foreach ($emails as $email_number) {
                    $overview = imap_fetch_overview($imap, $email_number, 0);
                    $from = strtolower($overview[0]->from ?? '');

                    foreach ($recordsToInsert as $record) {
                        if ($record['is_email']) {
                            // Email match
                            if (stripos($from, strtolower($record['subscriber_value'])) !== false) {
                                $repliedSubscribers[] = $record;
                                break;
                            }
                        } else {
                            // Name match
                            if (stripos($from, strtolower($record['subscriber_value'])) !== false) {
                                $repliedSubscribers[] = $record;
                                break;
                            }
                        }
                    }
                }
            }
            imap_close($imap);

            // ✅ Update analytics for all subscribers
            foreach ($recordsToInsert as $record) {
                $hasReplied = collect($repliedSubscribers)->contains(function ($replied) use ($record) {
                    return $replied['subscriber_value'] === $record['subscriber_value'];
                });
                Analytics::updateOrCreate(
                    [
                        'subscriber_id' => $record['subscriber_id'],
                        'campaign_id'   => $record['campaign_id'],
                    ],
                    [
                        'replied'     => $hasReplied ? 1 : 0,
                        'replied_at'  => $hasReplied ? now() : null,
                    ]
                );
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Replies checked successfully!',
                'total_subscribers' => count($recordsToInsert),
                'total_replied' => count($repliedSubscribers)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function duplicate(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:campaigns,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }

        $original = Campaign::with(['emailAccounts', 'list', 'template', 'followups'])->findOrFail($request->id);

        DB::transaction(function () use ($original) {
            $duplicate = $original->replicate();
            $duplicate->status = Campaign::STATUS_DRAFT;
            $duplicate->name = $original->name . ' (Copy)';
            $duplicate->save();

            // Sync accounts
            $duplicate->emailAccounts()->attach($original->emailAccounts->pluck('id')->toArray());

            // Clone followups
            foreach ($original->followups as $followup) {
                $newFollowup = $followup->replicate();
                $newFollowup->campaign_id = $duplicate->id;
                $newFollowup->save();
            }

            // Clone analytics
            Analytics::where('campaign_id', $original->id)
                ->chunk(1000, function ($rows) use ($duplicate) {
                    foreach ($rows as $row) {
                        $newRow = $row->replicate();
                        $newRow->campaign_id = $duplicate->id;
                        $newRow->subscriber_id = $row->subscriber_id;
                        $newRow->sent_at = null;
                        $newRow->sent = 0;
                        $newRow->replied = 0;
                        $newRow->response = null;
                        $newRow->email_account_id = null;
                        $newRow->followup_1 = null;
                        $newRow->followup_2 = null;
                        $newRow->skipped = 0;
                        $newRow->blacklisted = 0;
                        $newRow->bounced = 0;
                        $newRow->save();
                    }
                });
        });

        return $this->sendSuccess('Campaign duplicated successfully!');
    }


    public function delete(Request $request)
    {
        foreach ($request->id as $id) {
            $campaign = Campaign::find($id);
            $campaign->delete();
        }
        return $this->sendSuccess('Campaigns deleted successfully!');
    }
    public function revise(Request $request)
    {
        $campaign = Campaign::find($request->id);
        $campaign->update([
            'status' => Campaign::STATUS_LAUNCHED
        ]);
        foreach ($campaign->analytics as $row) {
            $row->update([
                'sent' => false,
                'sent_at' => null,
                'response' => null,
                'replied' => 0,
                'skipped' => 0,
                'bounced' => 0,
                'blacklisted' => 0,
                'error' => null,
            ]);
        }
        return $this->sendSuccess('Campaign revised successfully!');
    }
}
