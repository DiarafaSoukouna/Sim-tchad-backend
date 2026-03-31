<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\ActorTypeController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpeculationController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductionAreaController;
use App\Http\Controllers\UniteOfMeasureController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\AlertController;








/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 
Route::apiResource('actor_type', ActorTypeController::class);
Route::apiResource('actor', ActorController::class);
Route::post('actor/login', [ActorController::class, 'auth']);
Route::post('actor/changePassword/{id}', [ActorController::class, 'changePassword']);
Route::apiResource('sector', SectorController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('speculation', SpeculationController::class);
Route::apiResource('store', StoreController::class);
Route::apiResource('setting', SettingController::class);
Route::apiResource('production_area', ProductionAreaController::class);
Route::apiResource('unite_of_measure', UniteOfMeasureController::class);
Route::apiResource('product_type', ProductTypeController::class);
Route::get('product_type_with_attributes', [ProductTypeController::class, 'indexWithAttributes']);
Route::get('attributes_by_product_type/{id}', [ProductTypeController::class, 'attributesByProductType']);
Route::apiResource('product', ProductController::class);
Route::get('products_by_type/{type_id}', [ProductController::class, 'showWithAttributes']);
Route::apiResource('language', LanguageController::class);
Route::apiResource('currency', CurrencyController::class);
Route::get('product/namesProducts/{id}', [ProductController::class, 'getProductNamesInOtherLanguages']);
Route::post('product/addLanguagesToProduct/{id}', [ProductController::class, 'addLanguagesToProduct']);
Route::post('product/addCurrenciesToProduct/{id}', [ProductController::class, 'addCurrenciesToProduct']);
Route::post('product/deleteLanguageFromProduct/{id}/{language_id}', [ProductController::class, 'deleteLanguageFromProduct']);
Route::apiResource('order', OrderController::class);
Route::post('order/updateStatus/{id}', [OrderController::class, 'updateStatus']);
Route::apiResource('product_review', ProductReviewController::class);
Route::post('subscribe', [SubscriptionController::class, 'subscribe']);
Route::post('unsubscribe', [SubscriptionController::class, 'unsubscribe']);
Route::get('followers/{id}', [SubscriptionController::class, 'followersActor']);
Route::get('following/{id}', [SubscriptionController::class, 'followingActor']);
Route::apiResource('post', PostController::class);
Route::post('product/status/{id}', [ProductController::class, 'changeProductStatus']);
Route::get('notification/{actorId}', [NotificationController::class, 'index']);
Route::post('notification', [NotificationController::class, 'store']);
Route::delete('notification/{id}', [NotificationController::class, 'destroy']);
Route::post('notification/markAsRead/{id}', [NotificationController::class, 'markAsRead']);
Route::post('email/send', [MessagesController::class, 'SendMailTo']);
Route::apiResource('alert', AlertController::class);
// Route::post('whatsapp/send', [MessagesController::class, 'Whatsapp']);
