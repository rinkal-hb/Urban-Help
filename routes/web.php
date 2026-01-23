<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\temp\PagesController;
use App\Http\Controllers\temp\DashboardsController;
use App\Http\Controllers\temp\TaskController;
use App\Http\Controllers\temp\AuthenticationController;
use App\Http\Controllers\temp\ErrorController;
use App\Http\Controllers\temp\UielementsController;
use App\Http\Controllers\temp\UtilitiesController;
use App\Http\Controllers\temp\FormsController;
use App\Http\Controllers\temp\AdvanceduiController;
use App\Http\Controllers\temp\WidgetsController;
use App\Http\Controllers\temp\AppsController;
use App\Http\Controllers\temp\TablesController;
use App\Http\Controllers\temp\ChartsController;
use App\Http\Controllers\temp\MapsController;
use App\Http\Controllers\temp\IconsController;


// use App\Http\Controllers\temp\Controller;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('', [Controller::class, 'index']);





// DASHBOARDS //
Route::prefix('temp')->group(
    function () {
        Route::get('/', [DashboardsController::class, 'index']);
        Route::get('index', [DashboardsController::class, 'index']);
        Route::get('index2', [DashboardsController::class, 'index2']);
        Route::get('index3', [DashboardsController::class, 'index3']);
        Route::get('index4', [DashboardsController::class, 'index4']);
        Route::get('index5', [DashboardsController::class, 'index5']);
        Route::get('index6', [DashboardsController::class, 'index6']);
        Route::get('index7', [DashboardsController::class, 'index7']);
        Route::get('index8', [DashboardsController::class, 'index8']);
        Route::get('index9', [DashboardsController::class, 'index9']);
        Route::get('index10', [DashboardsController::class, 'index10']);
        Route::get('index11', [DashboardsController::class, 'index11']);
        Route::get('index12', [DashboardsController::class, 'index12']);

        // PAGES //
        Route::get('aboutus', [PagesController::class, 'aboutus']);
        Route::get('blog', [PagesController::class, 'blog']);
        Route::get('blog-details', [PagesController::class, 'blog_details']);
        Route::get('blog-create', [PagesController::class, 'blog_create']);
        Route::get('chat', [PagesController::class, 'chat']);
        Route::get('contacts', [PagesController::class, 'contacts']);
        Route::get('contactus', [PagesController::class, 'contactus']);
        Route::get('add-products', [PagesController::class, 'add_products']);
        Route::get('cart', [PagesController::class, 'cart']);
        Route::get('checkout', [PagesController::class, 'checkout']);
        Route::get('edit-products', [PagesController::class, 'edit_products']);
        Route::get('order-details', [PagesController::class, 'order_details']);
        Route::get('orders', [PagesController::class, 'orders']);
        Route::get('products', [PagesController::class, 'products']);
        Route::get('products-details', [PagesController::class, 'products_details']);
        Route::get('products-list', [PagesController::class, 'products_list']);
        Route::get('wishlist', [PagesController::class, 'wishlist']);
        Route::get('mail', [PagesController::class, 'mail']);
        Route::get('mail-settings', [PagesController::class, 'mail_settings']);
        Route::get('empty-page', [PagesController::class, 'empty_page']);
        Route::get('faqs', [PagesController::class, 'faqs']);
        Route::get('filemanager', [PagesController::class, 'filemanager']);
        Route::get('invoice-create', [PagesController::class, 'invoice_create']);
        Route::get('invoice-details', [PagesController::class, 'invoice_details']);
        Route::get('invoice-list', [PagesController::class, 'invoice_list']);
        Route::get('landing', [PagesController::class, 'landing']);
        Route::get('landing-jobs', [PagesController::class, 'landing_jobs']);
        Route::get('notifications', [PagesController::class, 'notifications']);
        Route::get('pricing', [PagesController::class, 'pricing']);
        Route::get('profile', [PagesController::class, 'profile']);
        Route::get('reviews', [PagesController::class, 'reviews']);
        Route::get('teams', [PagesController::class, 'teams']);
        Route::get('terms-conditions', [PagesController::class, 'terms_conditions']);
        Route::get('timeline', [PagesController::class, 'timeline']);
        Route::get('todo-list', [PagesController::class, 'todo_list']);

        // TASK //
        Route::get('task-kanban-board', [TaskController::class, 'task_kanban_board']);
        Route::get('task-listview', [TaskController::class, 'task_listview']);
        Route::get('task-details', [TaskController::class, 'task_details']);

        // AUTHENTICATION //
        Route::get('comingsoon', [AuthenticationController::class, 'comingsoon']);
        Route::get('createpassword-basic', [AuthenticationController::class, 'createpassword_basic']);
        Route::get('createpassword-cover', [AuthenticationController::class, 'createpassword_cover']);
        Route::get('lockscreen-basic', [AuthenticationController::class, 'lockscreen_basic']);
        Route::get('lockscreen-cover', [AuthenticationController::class, 'lockscreen_cover']);
        Route::get('resetpassword-basic', [AuthenticationController::class, 'resetpassword_basic']);
        Route::get('resetpassword-cover', [AuthenticationController::class, 'resetpassword_cover']);
        Route::get('signup-basic', [AuthenticationController::class, 'signup_basic']);
        Route::get('signup-cover', [AuthenticationController::class, 'signup_cover']);
        Route::get('signin-basic', [AuthenticationController::class, 'signin_basic']);
        Route::get('signin-cover', [AuthenticationController::class, 'signin_cover']);
        Route::get('twostep-verification-basic', [AuthenticationController::class, 'twostep_verification_basic']);
        Route::get('twostep-verification-cover', [AuthenticationController::class, 'twostep_verification_cover']);
        Route::get('under-maintenance', [AuthenticationController::class, 'under_maintenance']);

        // ERROR //
        Route::get('error401', [ErrorController::class, 'error401']);
        Route::get('error404', [ErrorController::class, 'error404']);
        Route::get('error500', [ErrorController::class, 'error500']);

        // UI ELEMENTS //
        Route::get('alerts', [UielementsController::class, 'alerts']);
        Route::get('badges', [UielementsController::class, 'badges']);
        Route::get('breadcrumbs', [UielementsController::class, 'breadcrumbs']);
        Route::get('buttons', [UielementsController::class, 'buttons']);
        Route::get('buttongroups', [UielementsController::class, 'buttongroups']);
        Route::get('cards', [UielementsController::class, 'cards']);
        Route::get('dropdowns', [UielementsController::class, 'dropdowns']);
        Route::get('images-figures', [UielementsController::class, 'images_figures']);
        Route::get('listgroups', [UielementsController::class, 'listgroups']);
        Route::get('navs-tabs', [UielementsController::class, 'navs_tabs']);
        Route::get('object-fit', [UielementsController::class, 'object_fit']);
        Route::get('paginations', [UielementsController::class, 'paginations']);
        Route::get('popovers', [UielementsController::class, 'popovers']);
        Route::get('progress', [UielementsController::class, 'progress']);
        Route::get('spinners', [UielementsController::class, 'spinners']);
        Route::get('toasts', [UielementsController::class, 'toasts']);
        Route::get('tooltips', [UielementsController::class, 'tooltips']);
        Route::get('typography', [UielementsController::class, 'typography']);

        // UTILITIES //
        Route::get('avatars', [UtilitiesController::class, 'avatars']);
        Route::get('borders', [UtilitiesController::class, 'borders']);
        Route::get('breakpoints', [UtilitiesController::class, 'breakpoints']);
        Route::get('colors', [UtilitiesController::class, 'colors']);
        Route::get('columns', [UtilitiesController::class, 'columns']);
        Route::get('css-grid', [UtilitiesController::class, 'css_grid']);
        Route::get('flex', [UtilitiesController::class, 'flex']);
        Route::get('gutters', [UtilitiesController::class, 'gutters']);
        Route::get('helpers', [UtilitiesController::class, 'helpers']);
        Route::get('positions', [UtilitiesController::class, 'positions']);
        Route::get('more', [UtilitiesController::class, 'more']);

        // FORMS //
        Route::get('form-inputs', [FormsController::class, 'form_inputs']);
        Route::get('form-check-radios', [FormsController::class, 'form_check_radios']);
        Route::get('form-input-groups', [FormsController::class, 'form_input_groups']);
        Route::get('form-select', [FormsController::class, 'form_select']);
        Route::get('form-range', [FormsController::class, 'form_range']);
        Route::get('form-input-masks', [FormsController::class, 'form_input_masks']);
        Route::get('form-file-uploads', [FormsController::class, 'form_file_uploads']);
        Route::get('form-datetime-pickers', [FormsController::class, 'form_datetime_pickers']);
        Route::get('form-color-pickers', [FormsController::class, 'form_color_pickers']);
        Route::get('floating-labels', [FormsController::class, 'floating_labels']);
        Route::get('form-layouts', [FormsController::class, 'form_layouts']);
        Route::get('quill-editor', [FormsController::class, 'quill_editor']);
        Route::get('form-validations', [FormsController::class, 'form_validations']);
        Route::get('form-select2', [FormsController::class, 'form_select2']);

        // ADVANCED UI //
        Route::get('accordions-collapse', [AdvanceduiController::class, 'accordions_collapse']);
        Route::get('carousel', [AdvanceduiController::class, 'carousel']);
        Route::get('draggable-cards', [AdvanceduiController::class, 'draggable_cards']);
        Route::get('modals-closes', [AdvanceduiController::class, 'modals_closes']);
        Route::get('navbars', [AdvanceduiController::class, 'navbars']);
        Route::get('offcanvas', [AdvanceduiController::class, 'offcanvas']);
        Route::get('placeholders', [AdvanceduiController::class, 'placeholders']);
        Route::get('ratings', [AdvanceduiController::class, 'ratings']);
        Route::get('scrollspy', [AdvanceduiController::class, 'scrollspy']);
        Route::get('swiperjs', [AdvanceduiController::class, 'swiperjs']);

        // WIDGETS //
        Route::get('widgets', [WidgetsController::class, 'widgets']);

        // APPS //
        Route::get('full-calendar', [AppsController::class, 'full_calendar']);
        Route::get('gallery', [AppsController::class, 'gallery']);
        Route::get('sweet-alerts', [AppsController::class, 'sweet_alerts']);
        Route::get('projects-list', [AppsController::class, 'projects_list']);
        Route::get('projects-overview', [AppsController::class, 'projects_overview']);
        Route::get('projects-create', [AppsController::class, 'projects_create']);
        Route::get('job-details', [AppsController::class, 'job_details']);
        Route::get('job-company-search', [AppsController::class, 'job_company_search']);
        Route::get('job-search', [AppsController::class, 'job_search']);
        Route::get('job-post', [AppsController::class, 'job_post']);
        Route::get('job-list', [AppsController::class, 'job_list']);
        Route::get('job-candidate-search', [AppsController::class, 'job_candidate_search']);
        Route::get('job-candidate-details', [AppsController::class, 'job_candidate_details']);
        Route::get('nft-marketplace', [AppsController::class, 'nft_marketplace']);
        Route::get('nft-details', [AppsController::class, 'nft_details']);
        Route::get('nft-create', [AppsController::class, 'nft_create']);
        Route::get('nft-wallet-integration', [AppsController::class, 'nft_wallet_integration']);
        Route::get('nft-live-auction', [AppsController::class, 'nft_live_auction']);
        Route::get('crm-contacts', [AppsController::class, 'crm_contacts']);
        Route::get('crm-companies', [AppsController::class, 'crm_companies']);
        Route::get('crm-deals', [AppsController::class, 'crm_deals']);
        Route::get('crm-leads', [AppsController::class, 'crm_leads']);
        Route::get('crypto-transactions', [AppsController::class, 'crypto_transactions']);
        Route::get('crypto-currency-exchange', [AppsController::class, 'crypto_currency_exchange']);
        Route::get('crypto-buy-sell', [AppsController::class, 'crypto_buy_sell']);
        Route::get('crypto-marketcap', [AppsController::class, 'crypto_marketcap']);
        Route::get('crypto-wallet', [AppsController::class, 'crypto_wallet']);

        // TABLES //
        Route::get('tables', [TablesController::class, 'tables']);
        Route::get('grid-tables', [TablesController::class, 'grid_tables']);
        Route::get('data-tables', [TablesController::class, 'data_tables']);

        // CHARTS //
        Route::get('apex-line-charts', [ChartsController::class, 'apex_line_charts']);
        Route::get('apex-area-charts', [ChartsController::class, 'apex_area_charts']);
        Route::get('apex-column-charts', [ChartsController::class, 'apex_column_charts']);
        Route::get('apex-bar-charts', [ChartsController::class, 'apex_bar_charts']);
        Route::get('apex-mixed-charts', [ChartsController::class, 'apex_mixed_charts']);
        Route::get('apex-rangearea-charts', [ChartsController::class, 'apex_rangearea_charts']);
        Route::get('apex-timeline-charts', [ChartsController::class, 'apex_timeline_charts']);
        Route::get('apex-candlestick-charts', [ChartsController::class, 'apex_candlestick_charts']);
        Route::get('apex-boxplot-charts', [ChartsController::class, 'apex_boxplot_charts']);
        Route::get('apex-bubble-charts', [ChartsController::class, 'apex_bubble_charts']);
        Route::get('apex-scatter-charts', [ChartsController::class, 'apex_scatter_charts']);
        Route::get('apex-heatmap-charts', [ChartsController::class, 'apex_heatmap_charts']);
        Route::get('apex-treemap-charts', [ChartsController::class, 'apex_treemap_charts']);
        Route::get('apex-pie-charts', [ChartsController::class, 'apex_pie_charts']);
        Route::get('apex-radialbar-charts', [ChartsController::class, 'apex_radialbar_charts']);
        Route::get('apex-radar-charts', [ChartsController::class, 'apex_radar_charts']);
        Route::get('apex-polararea-charts', [ChartsController::class, 'apex_polararea_charts']);
        Route::get('chartjs-charts', [ChartsController::class, 'chartjs_charts']);
        Route::get('echarts', [ChartsController::class, 'echarts']);
        Route::get('chartjs', [ChartsController::class, 'chartjs']);
        Route::get('echartjs', [ChartsController::class, 'echartjs']);

        // MAPS //
        Route::get('google-maps', [MapsController::class, 'google_maps']);
        Route::get('leaflet-maps', [MapsController::class, 'leaflet_maps']);
        Route::get('vector-maps', [MapsController::class, 'vector_maps']);

        // ICONS //
        Route::get('icons', [IconsController::class, 'icons']);
    }
);

// Route::get('/', [HomeController::class, 'index'])->name('Home');
// Route::get('/stay-details', [HomeController::class, 'StayDetails'])->name('StayDetails');
// Route::get('/stay-search', [HomeController::class, 'StaySearch'])->name('StaySearch');
// Route::get('/auth', [AuthController::class, 'Authuser'])->name('login');
// Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.otp');

// Placeholder routes for host and guest dashboards (to be implemented later)
// Route::middleware('auth')->group(function () {
//     Route::get('/host/dashboard', function () {
//         return view('host.dashboard'); // Create this view later
//     })->name('host.dashboard');

//     Route::get('/guest/dashboard', function () {
//         return view('guest.dashboard'); // Create this view later  
//     })->name('guest.dashboard');
// });

// Route::get('unauthorized', function () {
//     return view('pages.error401');
// })->name('login.401');

// Simple chat routes for testing (without authentication)
// use App\Http\Controllers\Admin\ChatManageController;

// Route::prefix('test-chat')->group(function () {
//     Route::get('/', [ChatManageController::class, 'testIndex'])->name('test.chat');
//     Route::get('/messages/{userId}', [ChatManageController::class, 'getMessages'])->name('test.chat.messages');
//     Route::post('/send', [ChatManageController::class, 'testSend'])->name('test.chat.send');
//     Route::post('/mark-seen/{userId}', [ChatManageController::class, 'markAsSeen'])->name('test.chat.mark-seen');
//     Route::get('/unread-count', [ChatManageController::class, 'getUnreadCount'])->name('test.chat.unread-count');
//     Route::get('/search-users', [ChatManageController::class, 'searchUsers'])->name('test.chat.search-users');
// });

// // Debug chat route (no auth required)
// Route::get('/debug-chat', [ChatManageController::class, 'debugIndex'])->name('debug.chat');
