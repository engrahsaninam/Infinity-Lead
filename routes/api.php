<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BillingInformationController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SendingServerController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\User\ApiKeyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\BankAccountController;
use App\Http\Controllers\User\BlacklistController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\ColumnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\NameValidationController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\User\GoogleSheetController;
use App\Http\Controllers\User\LeadFinderController;
use App\Http\Controllers\User\CampaignController;
use App\Http\Controllers\User\EmailAccountController;
use App\Http\Controllers\User\LeadController;
use App\Http\Controllers\User\ListsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SubscriptionController;
use App\Http\Controllers\User\TagController;
use App\Http\Controllers\User\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);
Route::post('check-auth', [AuthController::class, 'checkAuth']);
Route::post('google-auth', [AuthController::class, 'googleAuth']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);


Route::post('sales-navigator-leads', [AuthController::class, 'salesNavigatorLeads']);
Route::post('auth-permissions', [AuthController::class, 'authPermissions']);


Route::post('admin/login-via-id', [CustomerController::class, 'login']);
Route::post('user/back-to-admin', [CustomerController::class, 'impersonateAdmin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('google/gmail-readonly', [AuthController::class, 'gmailAccessWithGoogle']);
    Route::get('google/sheet-access', [AuthController::class, 'sheetAccessWithGoogle']);

    Route::post('google-client-id', [AuthController::class, 'googleClientId']);
    Route::post('excel-client-id', [AuthController::class, 'excelClientId']);
    Route::post('auth-user', [AuthController::class, 'authUser']);
    Route::prefix('admin')->group( function () {
        Route::prefix('users')->controller(CustomerController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('create', 'create');
            Route::post('edit', 'edit');
            Route::post('assign', 'assign');
            Route::post('action', 'action');
            Route::post('delete', 'delete');
        });
        Route::prefix('admins')->controller(AdminController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('create', 'create');
            Route::post('edit', 'edit');
        });


        Route::prefix('subscriptions')->controller(\App\Http\Controllers\Admin\SubscriptionController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('action', 'action');
            Route::post('recurring', 'recurring');
            Route::post('replenish', 'replenish');
            Route::post('terminate', 'terminate');
        });


        Route::prefix('name-validation')->controller(NameValidationController::class)->group(function () {
            Route::get('fetch', 'fetch');
            Route::post('store-remove', 'storeRemove');
            Route::post('store-replace', 'storeReplace');
            Route::post('delete', 'delete');
            Route::post('import-replace', 'importReplace');
        });

        Route::prefix('testimonials')->controller(TestimonialController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });
        
        Route::prefix('sending_servers')->controller(SendingServerController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('edit', 'edit');
            Route::post('store', 'store');
            Route::post('test-connection', 'testConnection');
            Route::post('delete', 'delete');
        });

        Route::prefix('payment-methods')->controller(PaymentMethodController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('update', 'update');
            Route::post('disable', 'disable');
        });

        Route::prefix('faqs')->controller(FaqController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });
        Route::prefix('currencies')->controller(CurrencyController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });
        Route::prefix('plans')->controller(PlanController::class)->group(function () {
            Route::post('currencies', 'currencies');
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });
        Route::prefix('teams')->controller(TeamController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });
        Route::prefix('settings')->controller(SettingController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
        });



    });
    Route::prefix('user')->group( function () {
        Route::prefix('dashboard')->controller(\App\Http\Controllers\User\DashboardController::class)->group(function () {
            Route::post('quota', 'quota');
            Route::post('notifications', 'notifications');
            Route::post('notif_read_redirect', 'notif_read_redirect');
        });
        Route::prefix('tags')->controller(TagController::class)->group(function () {
            Route::post('count', 'count');
            Route::post('fetch', 'fetch');
            Route::post('edit', 'edit');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
            Route::post('show', 'show');
            Route::post('fetch_column', 'fetch_column');
            Route::post('mapping', 'mapping');
        });
        Route::prefix('templates')->controller(TemplateController::class)->group(function () {
            Route::post('count', 'count');
            Route::post('fetch', 'fetch');
            Route::post('edit', 'edit');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
            Route::post('show', 'show');
            Route::post('csv', 'csv');
        });

        Route::prefix('lists')->controller(ListsController::class)->group(function () {
            Route::post('count', 'count');
            Route::post('fetch', 'fetch');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
            Route::post('show', 'show');
            Route::post('csv', 'csv');
            Route::post('remove-csv', 'removeCsv');
            Route::post('pagination', 'pagination');
            Route::post('read-sheet', 'readSheet');
            Route::post('csv-headers', 'csvHeaders');
            Route::post('google-headers', 'googleHeaders');
            Route::post('progress', 'progress');

        });


        Route::prefix('blacklists')->controller(BlacklistController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('count', 'count');
            Route::post('store', 'store');
            Route::post('delete', 'delete');
            Route::post('parse-csv', 'parseCsv');
            Route::post('import', 'import');
        });
        Route::prefix('websites')->controller(WebsiteController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('count', 'count');
            Route::post('store', 'store');
            Route::post('update', 'update');
            Route::post('delete', 'delete');
            Route::post('parse-csv', 'parseCsv');
            Route::post('import', 'import');
        });

        Route::prefix('campaigns')->controller(CampaignController::class)->group(function () {
            Route::post('list', 'list');
            Route::post('lists', 'lists');
            Route::post('templates', 'templates');
            Route::post('duplicate', 'duplicate');
            Route::post('count', 'count');
            Route::post('fetch', 'fetch');
            Route::post('edit', 'edit');
            Route::post('show', 'show');
            Route::post('rows', 'rows');
            Route::post('name', 'name');
            Route::post('sheet_type', 'sheet_type');
            Route::post('controls', 'controls');
            Route::post('accounts', 'accounts');
            Route::post('csv', 'csv');
            Route::post('more-csv', 'moreCsv');
            Route::post('remove-csv', 'removeCsv');
            Route::post('delete', 'delete');
            Route::post('read-sheet', 'readSheet');
            Route::post('days', 'days');
            Route::post('timezone', 'timezone');
            Route::post('time', 'time');
            Route::post('slider', 'slider');
            Route::post('launch', 'launch');
            Route::post('deliverability', 'deliverability');
            Route::post('email-content', 'emailContent');
            Route::post('delete-signature', 'deleteSignature');
            Route::post('delete-followup', 'deleteFollowup');
            Route::post('play-pause', 'playPause');
            Route::post('message', 'message');
            Route::post('signature', 'signature');
            Route::post('add-followup', 'addFollowup');
            Route::post('analytics', 'analytics');
            Route::post('email-mapping', 'emailMapping');
            Route::post('pagination-rows', 'paginationRows');
            Route::post('subject', 'subject');
            Route::post('save-message', 'saveMessage');
            Route::post('save-signature', 'saveSignature');
            Route::post('sync-followups', 'syncFollowups');
            Route::post('attachment', 'attachment');
            Route::post('delete-attachment', 'deleteAttachment');
            Route::post('csv-progress', 'csvProgress');
            Route::post('revise', 'revise');
            Route::post('pagination', 'pagination');
            Route::post('check-reply', 'checkReply');
        });
        Route::prefix('google-sheets')->controller(GoogleSheetController::class)->group(function () {
            Route::post('auth', 'auth');
            Route::post('fetch', 'fetch');
        });
        Route::prefix('leads')->controller(LeadController::class)->group(function () {
            Route::post('create', 'create');
            Route::post('update', 'update');
            Route::post('fetch', 'fetch');
            Route::post('index', 'index');
            Route::post('delete', 'delete');
            Route::post('parse-csv', 'parseCsv');
            Route::post('import', 'import');
            Route::post('clear_notification', 'clearNotification');
            Route::post('delete', 'delete');
        });
        Route::prefix('email')->controller(EmailController::class)->group(function () {
            Route::post('send', 'send');
            Route::post('check-reply', 'checkReply');
        });
        Route::prefix('column')->controller(ColumnController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('create', 'create');
            Route::post('delete', 'delete');
            Route::post('update', 'update');
        });
        Route::prefix('api-keys')->controller(ApiKeyController::class)->group(function () {
            Route::post('fetch/open-ai', 'fetch_openAi');
            Route::post('update/open-ai', 'update_openAi');
            Route::post('delete/open-ai', 'delete_openAi');

            Route::post('fetch/verification-tool', 'fetch_verificationTool');
            Route::post('update/verification-tool', 'update_verificationTool');
            Route::post('delete/verification-tool', 'delete_verificationTool');

        });
        Route::prefix('billing-information')->controller(BillingInformationController::class)->group(function () {
            Route::post('update', 'update');
            Route::post('fetch', 'fetch');

        });
        Route::prefix('subscriptions')->controller(SubscriptionController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('recurring', 'recurring');
            Route::post('cancel', 'cancel');
            
        });
            Route::prefix('bank-accounts')->controller(BankAccountController::class)->group(function () {
            Route::post('methods', 'methods');
            Route::post('plans', 'plans');
            Route::post('banks', 'banks');
            Route::post('stripe-store', 'stripe_store');
            Route::post('process-payment', 'processPayment');
            Route::post('offline-payment-pending', 'offlinePaymentPending');
            Route::post('refresh-offline-status', 'refreshOfflineStatus');
            Route::post('assigned', 'assigned');
            Route::post('publish-key', 'publishKey');
        });
        Route::prefix('chat')->controller(ChatController::class)->group(function () {
            Route::post('compose', 'compose');
            Route::post('replies', 'replies');
        });
        Route::prefix('email-accounts')->controller(EmailAccountController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('delete', 'delete');
            Route::post('range', 'range');
            Route::post('smtp-create', 'smtpCreate');
        });
        Route::prefix('profile')->controller(ProfileController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('update', 'update');
            Route::post('password', 'password');
            Route::post('profile', 'profile');
            Route::post('help', 'help');
        });
        Route::prefix('lead-finder')->controller(LeadFinderController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('delete', 'delete');
            Route::post('records', 'records');
            Route::post('upload-multiple', 'uploadMultiple');
            Route::post('sync-companies', 'syncCompanies');
            Route::post('sync-companies-database', 'syncCompaniesDatabase');
            Route::post('generate-emails', 'generateEmails');
            Route::post('verify-emails', 'verifyEmails');
        });
        Route::prefix('domains')->controller(DomainController::class)->group(function () {
            Route::post('fetch', 'fetch');
            Route::post('delete', 'delete');
            Route::post('store', 'store');
            Route::post('{id}/verify', 'verify');
        });
    });
});
