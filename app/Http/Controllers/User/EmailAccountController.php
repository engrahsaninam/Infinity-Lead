<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\User;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportException;

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Swift_SmtpTransport;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class EmailAccountController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $accounts = User::find(Auth::id())->accounts;
        return $this->sendSuccess('Accounts fetched successfully!', $accounts);
    }
    public function range(Request $request)
    {
        EmailAccount::find($request->id)->update([
            'per_minute' => $request->per_minute,
            'volume' => $request->volume,
        ]);
        return $this->sendSuccess('Updated successfully!');
    }
    public function delete(Request $request)
    {
        EmailAccount::find($request->id)->delete();
        return $this->sendSuccess('Email deleted successfully!');
    }
    public function smtpCreate_bk(Request $request)
    {
        $validators = Validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'host' => 'required',
            'username' => 'required',
            'port' => 'required',
            'encryption' => 'nullable|in:ssl,tls',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }

        try {
            $encryption = in_array($request->encryption, ['ssl', 'tls']) ? $request->encryption : null;
            $transport = Swift_SmtpTransport::newInstance($request->host, $request->port, $encryption)
                ->setAuthMode('LOGIN')
                ->setUsername($request->username)
                ->setPassword($request->password)
                ->setTimeout(120);
            $transport->start();
        } catch (\Exception $e) {
            Log::error('SMTP Connection Error', ['message' => $e->getMessage(),]);
            return $this->sendError('SMTP authentication failed: ' . $e->getMessage(), 421);
        }
        $request->merge(['user_id' => Auth::id()]);
        $request->merge(['type' => EmailAccount::TYPE_SMTP]);
        if ($request->id) {
            $message = 'SMTP updated successfully!';
            EmailAccount::find($request->id)->update($request->all());
        } else {
            $message = 'SMTP added successfully!';
            EmailAccount::create($request->all());
        }
        return $this->sendSuccess($message);
    }

    public function smtpCreate(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required|email',
            'host' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required|numeric',
        ]);
        if ($validators->fails()) return $this->sendError($validators->messages(), 422);

        try {
            $request->from_email = $request->email;
            $type = $request->get('type', 'smtp'); // Default = smtp
            $testEmail = $request->email; // Use user's email for test recipient

            if ($type === 'smtp') {
                $host = trim($request->host);
                $encryption = $request->encryption ? strtolower(trim($request->encryption)) : null;

                if (str_contains($host, 'postmarkapp.com')) {
                    // Postmark prefers TLS on 587 if encryption is empty
                    $encryption = $encryption ?: 'tls';
                    $request->port = $request->port ?: 587;

                } elseif (str_contains($host, 'sparkpostmail.com')) {
                    // SparkPost STARTTLS = treat as TLS internally
                    if ($encryption && $encryption === 'starttls') {
                        $encryption = 'tls';
                    } elseif (!$encryption) {
                        $encryption = 'tls';
                    }
                    $request->port = $request->port ?: 587;

                } elseif (str_contains($host, 'postal.')) {
                    // Postal servers usually run unencrypted SMTP on port 25
                    $encryption = $encryption ?: null;
                    $request->port = $request->port ?: 25;
                }

                $config = [
                    'transport'  => 'smtp',
                    'host'       => $host,
                    'port'       => (int) $request->port,
                    'encryption' => $encryption,
                    'username'   => $request->username,
                    'password'   => $request->password,
                    'timeout'    => 15,
                    'auth_mode'  => null,
                    'stream'     => [
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                        ],
                    ],
                ];

                Config::set('mail.mailers.dynamic', $config);
                Config::set('mail.from.address', $request->from_email);
                Config::set('mail.from.name', 'SMTP Tester');

                Mail::mailer('dynamic')->raw("SMTP test successful!", function ($message) use ($testEmail) {
                    $message->to($testEmail)->subject("SMTP Test Email");
                });
            }

            $request->merge([
                'user_id' => Auth::id(),
                'type'    => $type,
            ]);

            EmailAccount::updateOrCreate(['id' => $request->id], $request->all());
            return $this->sendSuccess("SMTP/API verified & saved successfully!");

        } catch (\Swift_TransportException $e) {
            \Log::error("SMTP Connection Failed: " . $e->getMessage());
            return $this->sendError("Unable to connect to the SMTP server. Please check your SMTP host, port, encryption, and network connectivity.", 421);

        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            \Log::error("SMTP Auth Error: " . $e->getMessage());
            return $this->sendError("SMTP authentication failed. Please verify your username, password, and encryption settings.", 421);

        } catch (\Exception $e) {
            \Log::error("SMTP/API Error: " . $e->getMessage());
            return $this->sendError("Something went wrong while testing the SMTP connection. Please try again or contact support.", 421);
        }
    }

    public function testSmtpDummy()
    {
        // Route::get('/test-smtp-dummy', [EmailAccountController::class, 'testSmtpDummy']); // Define at web route to test it smtp code locally
        // 🔹 Multiple SMTP configs for testing
        $dummySmtpConfigs = [
            [
                'email' => 'michelle@b-conf.com', // authorized email
                'host' => 'postal.b-conf.com',
                'username' => 'cp',
                'password' => 'NhQOtMmjbZBb8WQKnOaxOXGQ',
                'port' => 25,
                'encryption' => null,
            ],
            [
                'email' => 'michelle@cp-event.com', // authorized email
                'host' => 'postal.cp-event.com',
                'username' => 'cp',
                'password' => 't5uVXAvhuVJOzNUYzh7OuGdZ',
                'port' => 25,
                'encryption' => null,
            ],
            [
                'email' => 'your-postmark-email@example.com',
                'host' => 'smtp.postmarkapp.com',
                'username' => '3ebf9422-4203-4cb6-b00c-ae708b6a840a',
                'password' => '3ebf9422-4203-4cb6-b00c-ae708b6a840a',
                'port' => 587,
                'encryption' => 'tls',
            ],
            [
                'email' => 'your-sparkpost-email@example.com',
                'host' => 'smtp.sparkpostmail.com',
                'username' => 'c39705b4b4a8d99f490ed190c56acdb4edfd354f',
                'password' => 'c39705b4b4a8d99f490ed190c56acdb4edfd354f',
                'port' => 587,
                'encryption' => 'tls',
            ],
        ];

        // Loop through all configs and test
        $results = [];
        foreach ($dummySmtpConfigs as $config) {
            $request = new Request($config);
            $results[] = $this->smtpCreate($request);
        }
        \Log::info("SMTP Testing Completed", $results);
        return response()->json([
            'status' => true,
            'tested_servers' => count($dummySmtpConfigs),
            'results' => $results,
        ]);
    }
}