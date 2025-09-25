<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Models\GoogleSheet;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\WelcomeEmail;
use App\Notifications\VerifyEmailNotification;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dompdf\Options;
class AuthController extends Controller
{
    //
    use CommonTraits;
    public function login(Request $request)
    {
        $validators = Validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!empty($user->remember_token)) return $this->sendError('Please verify your email first.', 403);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json([
                'status' => false,
                'token' => $token,
                'user' => $user,
            ], 200);
        } else {
            return $this->sendError('Invalid Credentials', 421);
        }
    }

    public function register(Request $request)
    {
        $validators = Validator($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ]
        ]);
        if ($validators->fails())
            return $this->sendError($validators->messages(), 422);

        $email = $request->email;
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        $googleDomains = ['gmail.com'];

        $verificationRequired = !in_array($domain, $googleDomains);

        $token = $verificationRequired ? Str::random(64) : null;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => $token,
            'email_verified_at' => $verificationRequired ? null : now(),
            'role' => User::ROLE_USER,
        ]);

        $apiToken = null;
        if ($verificationRequired) {
            $user->notify(new VerifyEmailNotification($user));
        } else {
            $user->notify(new WelcomeEmail());
            Auth::attempt($request->only('email', 'password'));
            $apiToken = $user->createToken('API Token')->plainTextToken;
        }

        return response()->json([
            'status' => $verificationRequired,
            'requires_verification' => $verificationRequired,
            'token' => $apiToken,
            'user' => $user,
            'message' => $verificationRequired
                ? 'Verification email sent! Please check your inbox.'
                : 'User registered and logged in successfully!'
        ], 200);
    }

    public function verifyEmail(Request $request)
    {
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired verification link.'
            ], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'remember_token' => null
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully.',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            try {
                $user->currentAccessToken()->delete();
            } catch (\Throwable $th) {
            }
        }
        return $this->sendSuccess("Logout Successfully!", true);

    }
    public function authUser(Request $request)
    {
        $user = new User();
        if (Auth::check()) {
            $id = Auth::id();
            $user = User::find($id);
            if ($user->role == User::ROLE_USER) {
                $subscription = $user->activeSubscription();
                if ($subscription && Carbon::parse($subscription->end)->lt(now())) {
                    $subscription->status =Subscription::STATUS_EXPIRED;
                    $subscription->save();
                    $user->status =User::STATUS_INACTIVE;
                    $user->save();
                }
            }
            if ($user->status === User::STATUS_RESTRICTED) {
                $user->tokens()->delete();
                return response()->json(['message' => 'Logged out!'], 421);
            }
        }
        return $this->sendSuccess("Auth user fetched!", $user);
    }
    public function authPermissions(Request $request)
    {
        $permissions = Auth::user()->role->permissions;
        $permissions = explode(',', $permissions);

        return $this->sendSuccess("Auth permissions fetched!", $permissions);
    }
    public function checkAuth()
    {
        return $this->sendSuccess("Auth checking!", ['id' => Auth::id()]);
    }
    public function googleAuth(Request $request)
    {
        $user = User::where('google_id', $request->token)->first();
        $user = Auth::loginUsingId($user->id);
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    public function googleClientId()
    {
        return $this->sendSuccess('Google client id fetched successfully!', env('GOOGLE_GMAIL_CLIENT_ID'));
    }
    public function excelClientId()
    {
        return $this->sendSuccess('Google sheet client id fetched successfully!', env('GOOGLE_SHEET_CLIENT_ID'));
    }

    public function registerWithGoogle()
    {
        config([
            'services.google' => config('services.google_register')
        ]);
        return Socialite::driver('google')->redirect();
    }
    public function loginWithGoogle()
    {
        config([
            'services.google' => config('services.google_login')
        ]);
        return Socialite::driver('google')->redirect();
    }
    public function googleRegisterCallback(Request $request)
    {
        config([
            'services.google' => config('services.google_register')
        ]);
        $googleUser = Socialite::driver('google')->stateless()->user();
        $fullName = $googleUser->getName();
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        $user = User::where('google_id', $googleUser->getId())->first();
        if ($user) {
            return redirect()->to('google-account-already-registered');
        } else {
            $avatarUrl = $googleUser->getAvatar();
            $imageName = uniqid() . '.jpg';
            $imageContent = Http::get($avatarUrl);
            $path = '';
            if ($imageContent->successful()) {
                Storage::disk('public')->put('profile/' . $imageName, $imageContent->body());
                $path = '/storage/profile/' . $imageName;
            }
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()),
                'profile' => $path,
            ]);
            $user->notify(new WelcomeEmail());
            return redirect()->to('google-auth/' . $googleUser->getId());
        }
    }
    public function googleLoginCallback(Request $request)
    {
        config([
            'services.google' => config('services.google_login')
        ]);
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('google_id', $googleUser->getId())->first();
        /*if (!$user->profile){
            $avatarUrl = $googleUser->getAvatar();
            $imageName = uniqid() . '.jpg';
            $imageContent = Http::get($avatarUrl);
            $path='';
            if ($imageContent->successful()) {
                $path = Storage::disk('public')->put('profile/' . $imageName, $imageContent->body());
            }
            $user->update(['profile'=>$path]);
        }*/

        if ($user) {
            return redirect()->to('google-auth/' . $googleUser->getId());
        } else {
            return redirect()->to('/google-login-account-not-found');
        }
    }
    public function gmailAccessWithGoogle(Request $request)
    {
        config([
            'services.google' => config('services.google_gmail')
        ]);

        $defaultScopes = [
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.send',
        ];

        /*$defaultScopes = [
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.compose',
        ];*/
        $scopes = $request->input('scopes', $defaultScopes);

        $redirectUrl = Socialite::driver('google')
            ->with(['access_type' => 'offline', 'prompt' => 'consent', 'state' => json_encode(['user_id' => Auth::id()])])
            ->scopes($scopes)
            ->stateless()
            ->redirect()
            ->getTargetUrl();
        return response()->json(['url' => $redirectUrl]);

    }
    public function sheetAccessWithGoogle(Request $request)
    {
        config([
            'services.google' => config('services.google_sheet')
        ]);
        $defaultScopes = [
            'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/spreadsheets',
        ];
        $scopes = $request->input('scopes', $defaultScopes);
        //->with(['access_type' => 'offline', 'prompt' => 'consent'])
        return Socialite::driver('google')
            ->scopes($scopes)
            ->with(['access_type' => 'offline', 'prompt' => 'consent', 'state' => json_encode(['user_id' => $request->user_id, 'list_id' => $request->list_id])])
            ->redirect();
    }

    public function googleSheetRedirect(Request $request)
    {
        $state = $request->state;
        $state = json_decode($state, true);
        config([
            'services.google' => config('services.google_sheet')
        ]);
        $user = Socialite::driver('google')->stateless()->user();
        GoogleSheet::updateOrCreate(
            ['user_id' => $state['user_id']],
            [
                'email' => $user->email,
                'google_access_token' => $user->token,
                'google_refresh_token' => $user->refreshToken,
                'google_token_expiry' => now()->addSeconds($user->expiresIn),

            ]
        );
        return redirect()->to('user/lists/show/' . $state['list_id']);
    }
    public function googleAccountAccess(Request $request)
    {
        $state = $request->state;
        $state = json_decode($state, true);
        config([
            'services.google' => config('services.google_gmail')
        ]);
        $googleUser = Socialite::driver('google')
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->stateless()->user();


        $imageName = uniqid() . '.jpg';
        $imageContent = Http::get($googleUser->getAvatar());
        $path = '';
        if ($imageContent->successful()) {
            Storage::disk('public')->put('accounts/' . $imageName, $imageContent->body());
            $path = '/storage/accounts/' . $imageName;
        }

        $data = [
            'type' => 'google',
            'user_id' => $state['user_id'],
            'email' => $googleUser->email,
            'name' => $googleUser->name,
            'access_token' => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'expires_in' => now()->addSeconds($googleUser->expiresIn),
            'profile' => $path,
        ];
        EmailAccount::updateOrCreate(
            ['google_id' => $googleUser->id],
            $data,
        );
        return redirect()->to('user/settings/email-accounts');
    }
    public function forgotPassword(Request $request)
    {
        $validators = Validator($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $customToken = $this->generateCustomToken($user);
            $user->notify(new CustomPasswordReset($customToken));
            return $this->sendSuccess('Password reset link sent successfully.');
        }
        return $this->sendError('Failed to send password reset link', 500);
    }
    private function generateCustomToken($user)
    {
        $token = Crypt::encryptString($user->email);
        return $token;
    }
    public function resetPassword(Request $request)
    {
        // Validate the incoming data
        $validators = Validator($request->all(), [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'token' => 'required|string',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        try {
            $decoded = Crypt::decryptString($request->token);
            $email = $decoded;
        } catch (\Exception $e) {
            return $this->sendError('Invalid or expired token.', 421);
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->sendSuccess('Password reset successfully.');
        }

        return $this->sendError('User not found or invalid token.', 421);
    }
    public function salesNavigatorLeads(Request $request)
    {
        dd($request->all());
    }
    public function exportColumnLeads($id)
    {
        $column = Column::findOrFail($id);
        $leads = $column->leads;
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=export_' . $column->id . date('-Y-m-d-h-i-a') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $columns = [
            'email',
            'first_name',
            'middle_name',
            'last_name',
            'job_title',
            'phone',
            'city',
            'company',
            'website',
            'linkedin',
        ];
        $callback = function () use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($leads as $lead) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $lead->{$col} ?? '';
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }
    public function invoice_print(Request $request) {
        $subscriptionId=$request->id;   
        $created_at=$request->passcode;   
        $userId=$request->user_id;   
        $user=User::find($userId);
        if(strtotime($user->created_at)===strtotime($created_at)){
            $subscription=Subscription::find($subscriptionId);
            if($subscription->user_id === $user->id){
                $user=$subscription->user;
                $billing=$user->billing_info;
                $cid=str_replace('/','-', $subscription->cid);
                return $this->domPDFPrint('exports.invoice', $cid, [
                    'subscription' => $subscription,
                    'billing'=> $billing,
                ]);
            }else{
                // user not authorized
            }
        }else{
            // user not authorized
        }
    }
    private function domPDFPrint($path, $fileName, $data)
    {
        ini_set('max_execution_time', 48000);
        ini_set('memory_limit', '4096M');
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $data['count'] = 0;
        $pdf = Pdf::loadView($path, $data);
        $pdf->render();
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $count = $pdf->getCanvas()->get_page_count();
        $data['count'] = $count;
        $pdf = Pdf::loadView($path, $data);
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        return $pdf->stream($fileName . '.pdf');
    }
}
