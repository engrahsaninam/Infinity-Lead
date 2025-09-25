<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GoogleSheet;
use App\Models\Lists;
use App\Traits\CommonTraits;
use Google\Client;
use Illuminate\Http\Request;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use Google\Service\Sheets;

class GoogleSheetController extends Controller
{
    //
    use CommonTraits;
    public function auth(Request $request){
        $user = Auth::user();
        $googleSheet = GoogleSheet::where('user_id', $user->id)->first();
        return $this->sendSuccess('Google Sheet fetched successfully!',$googleSheet);
    }
    public function fetch(Request $request)
    {
        $user = Auth::user();
        $googleSheet = GoogleSheet::where('user_id', $user->id)->first();
        if (!$googleSheet->google_sheets) {
            $googleSheet = GoogleSheet::where('user_id', $user->id)->first();
            $client=$this->authentication($googleSheet);
            try {
                $service = new Drive($client);
                $query = "mimeType='application/vnd.google-apps.spreadsheet'";
                $results = $service->files->listFiles(['q' => $query]);
                $files = [];
                foreach ($results->getFiles() as $result) {
                    $spreadsheetId = $result->getId();
                    $sheetsService = new Sheets($client);
                    $spreadsheet = $sheetsService->spreadsheets->get($spreadsheetId);
                    $sheets = [];
                    foreach ($spreadsheet->getSheets() as $sheet) {
                        $sheets[] = [
                            'id' => $sheet['properties']['sheetId'],
                            'name' => $sheet['properties']['title']
                        ];
                    }
                    $files[] = [
                        'id' => $spreadsheetId,
                        'name' => $result->getName(),
                        'sheets' => $sheets
                    ];
                }
                $googleSheet->update(['google_sheets' => json_encode($files)]);
                return response()->json($files);
            } catch (\Exception $exception) {
                $errorResponse = json_decode($exception->getMessage(), true);
                return $this->sendError('Google Sheet authentication required.', 421);
                //return  $this->sendError('Error : '.$errorResponse['error']['message'],421);
            }
        } else {
            return $this->sendSuccess('Google Sheet already connected');
        }

    }
    public function authentication($googleSheet){
        if (!$googleSheet || !$googleSheet->google_access_token) {
            return $this->sendError('Google Sheet authentication required.', 421);
        }
        $client = new Client();
        $client->setClientId(config('services.google_sheet.client_id'));
        $client->setClientSecret(config('services.google_sheet.client_secret'));
        //$client->setRedirectUri(config('services.google_sheet.redirect'));
        $client->setAccessToken($googleSheet->google_access_token);
        if ($client->isAccessTokenExpired()) {
            if (!$googleSheet->google_refresh_token) {
                return $this->sendError('Google Sheet authentication required.', 421);
            }
            $refreshResponse=$client->fetchAccessTokenWithRefreshToken($googleSheet->google_refresh_token);
            $newAccessToken = $client->getAccessToken();
            $expiresIn = isset($newAccessToken['expires_in']) ? $newAccessToken['expires_in'] : 3600; // default 1 hour

            $googleSheet->update([
                'google_access_token' => $newAccessToken['access_token'],
                'google_token_expiry' => now()->addSeconds($expiresIn),
            ]);
        }
        return $client;
    }

}
