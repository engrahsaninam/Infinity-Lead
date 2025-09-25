<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SendingServer;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class SendingServerController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request){
        $search = $request->search;
        $lists = SendingServer::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ( $query) use ($search) {
                $query->whereAny(['domain','host','port','username'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function edit(Request $request){
        $edit = SendingServer::find($request->id);
        return response()->json($edit);
    }
    
    public function store(Request $request){
        
        $validators = Validator($request->all(), [
            'type' => 'required|string',
            'domain' => 'required|string', 
            'host' => 'required|string', 
            'username' => 'required', 
            'password' => 'required', 
            'port' => 'required', 
            'encryption' => 'required', 
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        if(!$request->id){
            $request->merge(['user_id'=>Auth::id()]);
            SendingServer::create($request->all());
            return  $this->sendSuccess('Sending Server added successfully!');
        }else{
            SendingServer::find($request->id)->update($request->all());
            return  $this->sendSuccess('Sending Server updated successfully!');
        }
    }
    public function delete(Request $request){
        $test= SendingServer::find($request->id);
        if($test){
            $test->delete();
        }
        return  $this->sendSuccess('Sending Server deleted successfully!');
    }
    public function testConnection(Request $request){
        $server= SendingServer::find($request->id);
        $port = (int) $server->port;
        $config = [
            'transport' => 'smtp',
            'host' => $server->host,
            'port' => $port,
            'encryption' => $server->encryption,
            'username' => $server->username,
            'password' => $server->password,
            'timeout' => 10,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        ];
        Config::set('mail.mailers.temp_smtp', $config);
        Config::set('mail.from.address', $server->email);
        Config::set('mail.from.name', 'SMTP Tester');
        try {
            Mail::mailer('temp_smtp')->raw('SENDING SERVER TESTING', function ($message) {
                $message->to('emazeem07@yopmail.com')
                    ->subject('SENDING SERVER TEST');
            });
            $server->update(['status'=>SendingServer::STATUS_ACTIVE]);
            return $this->sendSuccess('Sending Server connected successfully!');

        } catch (\Exception $e) {
            return $this->sendError('SMTP verification failed: ' . $e->getMessage(), 421);
        }
    }
    
}