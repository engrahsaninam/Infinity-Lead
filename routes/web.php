<?php

use App\Http\Controllers\User\CampaignController;
use App\Http\Controllers\User\LeadFinderController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'landing'])->name('landing');
Route::get('welcome', [WebsiteController::class, 'welcome'])->name('welcome');
Route::get('campaigns', [WebsiteController::class, 'campaigns'])->name('campaigns');
Route::get('sales-crm', [WebsiteController::class, 'salesCrm'])->name('sales.crm');
Route::get('lead-finder', [WebsiteController::class, 'leadFinder'])->name('lead.finder');
Route::get('email-verifier', [WebsiteController::class, 'emailVerifier'])->name('email.verifier');
Route::get('email-finder', [WebsiteController::class, 'emailFinder'])->name('email.finder');
Route::get('pricing', [WebsiteController::class, 'pricing'])->name('pricing');
Route::get('about', [WebsiteController::class, 'about'])->name('about');
Route::get('contact', [WebsiteController::class, 'contact'])->name('contact');
Route::get('privacy-policy', [WebsiteController::class, 'privacy_policy'])->name('privacy.policy');
Route::get('terms-and-conditions', [WebsiteController::class, 'terms_and_conditions'])->name('terms.and.conditions');
Route::get('template/_view/{id}', [WebsiteController::class, 'template'])->name('template.show');

Route::get('google/login/redirect', [AuthController::class, 'loginWithGoogle']);
Route::get('google/register/redirect', [AuthController::class, 'registerWithGoogle']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('google/gmail-readonly', [AuthController::class, 'gmailAccessWithGoogle']);
});
Route::get('invoice/print/{id}/{passcode}/{user_id}', [AuthController::class, 'invoice_print']);
Route::get('column-lead/export/{columnId}', [AuthController::class, 'exportColumnLeads']);
Route::get('google/sheet-access', [AuthController::class, 'sheetAccessWithGoogle']);

Route::get('google/login/callback', [AuthController::class, 'googleLoginCallback']);
Route::get('google/register/callback', [AuthController::class, 'googleRegisterCallback']);
Route::get('google-account-access', [AuthController::class, 'googleAccountAccess']);
Route::get('google-sheet-redirect', [AuthController::class, 'googleSheetRedirect']);
Route::get('unsubscribe/row/{encryptedId}', [WebsiteController::class, 'unsubscribeRow']);

Route::get('campaigns/download/{id}/{filter}', [CampaignController::class, 'campaigns_download']);
Route::get('export/linked-in/records/{id}/{type}', [LeadFinderController::class, 'export'])->where('type', 'valid|invalid|all|skipped');

Route::get('/{any}', function () {
    return view('vue');
})->where('any', '.*');

// Test campaign mail to check for replies functionality
// Route::get('/test-send-email', function() {
//     $details = [
//         'subject' => 'Test Campaign Email',
//         'body' => 'This is a test email for reply tracking.'
//     ];
//     \Mail::to('raindeveloper01@gmail.com')->send(new \App\Mail\CampaignMail($details));
//     return 'Email sent!';
// });