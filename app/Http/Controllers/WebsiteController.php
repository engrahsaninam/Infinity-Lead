<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Team;
use App\Models\Template;
use App\Models\Testimonial;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;



use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    public function testSmtp()
    {
        $config = [
            'transport' => 'smtp',
            'host' => 'postal.cp-event.com',
            'port' => 25,
            'encryption' => null, // <-- important!
            'username' => 'cp',
            'password' => 'gchA4zDQx2WWLahAM9qifoDA',
            'timeout' => 10,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        ];

        

        Config::set('mail.mailers.smtp_test', $config);
        Config::set('mail.from.address', 'michelle@cp-event.com');
        Config::set('mail.from.name', 'SMTP Test');

        try {
            Mail::mailer('smtp_test')->raw('This is a test email using port 25.', function ($message) {
                $message->to('emazeem07@yopmail.com')
                    ->subject('SMTP Test over Port 25');
            });

            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function landing(CampaignService $campaignService)
    {

        
        $features = [
            array('icon' => 'assets/icons/why1.png', 'title' => 'Deliverability', 'description' => 'Avoid the spam trap. We optimize your domain and sender reputation to ensure your emails land in the right place.'),
            array('icon' => 'assets/icons/why2.png', 'title' => 'Scalability', 'description' => 'Send thousands of personalized emails without triggering alarms. Our infrastructure grows with your business.'),
            array('icon' => 'assets/icons/why3.png', 'title' => 'Expert Support', 'description' => 'Our team of email deliverability experts is here to guide you every step of the way.'),
            array('icon' => 'assets/icons/why4.png', 'title' => 'Tracking & Analytics', 'description' => 'Measure your open rates, click-throughs, and replies to refine your campaigns for maximum impact.'),
            array('icon' => 'assets/icons/why5.png', 'title' => 'Personalization', 'description' => 'Say goodbye to generic templates. We help you craft emails that feel tailor-made to each recipient.'),
        ];

        $faqs=[];
        foreach(Faq::all() as $faq){
            $faqs[]=[
                'q'=>$faq->question,
                'a'=>$faq->answer,
            ];
        }
        $testimonials = Testimonial::all();
        return view('landing', compact('features', 'faqs','testimonials'));
    }
    public function pricing()
    {
        return view('pricing');
    }
    public function about()
    {
        $teams = Team::all();
        return view('about',compact('teams'));
    }
    public function contact()
    {
        return view('contact');
    }
    public function welcome()
    {
        Auth::loginUsingId(1);

        return view('welcome');
    }
    public function privacy_policy()
    {
        $content = Setting::where('key', 'privacy-policy')->first();
        if ($content) {
            $content = $content->value;
        }
        return view('privacy_policy',compact('content'));
    }
    public function terms_and_conditions()
    {
        $content=Setting::where('key','terms-and-conditions')->first();
        if($content){
            $content = $content->value;
        }
        return view('terms_and_conditions',compact('content'));
    }
    public function campaigns()
    {
        return view('campaigns');
    }
    public function salesCrm()
    {
        return view('sales-crm');
    }
    public function leadFinder()
    {
        return view('lead-finder');
    }
    public function emailVerifier()
    {
        return view('email-verifier');
    }
    public function emailFinder()
    {
        return view('email-finder');
    }
    public function unsubscribeRow($encryptedId)
    {
        try {
            $rowId = Crypt::decryptString($encryptedId);
            $record = Analytics::findOrFail($rowId);
            $record->update(['blacklisted' => 1]);
            return response()->make('
            <html>
                <body>
                    <script>
                        alert("You have successfully unsubscribed.");
                        window.close();
                    </script>
                    <p>If the tab does not close automatically, please close it manually.</p>
                </body>
            </html>
        ');
        } catch (\Exception $e) {
            return response()->make('
            <html>
                <body>
                    <script>
                        alert("Invalid or expired link.");
                        window.close();
                    </script>
                </body>
            </html>
        ');
        }
    }
    public function template($id){
        $template=Template::find($id);
        $html=$template->body;
        return $html;
    }
}
