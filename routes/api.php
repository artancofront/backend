<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryAttributeController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\User\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




// Admin routes------------------------------------------------------------------
Route::prefix('admin/users')->group(function () {
    //Register-Login*
    Route::post('/ask-otp', [AuthController::class, 'askOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/login-password', [AuthController::class, 'loginWithPassword']);

    Route::middleware(['auth:user'])->group(function () {
        //Authenticated user
        Route::post('/reset-password', [AuthController::class, 'resetPasswordWithOTP']);
        Route::get('/show-profile', [UserController::class, 'showProfile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);

        //Users CRUD
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users,read');
        Route::get('{id}', [UserController::class, 'show'])->middleware('permission:users,read');
        Route::put('{id}', [UserController::class, 'update'])->middleware('permission:users,update');
        Route::delete('{id}', [UserController::class, 'destroy'])->middleware('permission:users,delete');
    });
});


Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles,read');         // List all roles
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:roles,read');     // Show role by ID
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles,create');        // Create new role
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:roles,update');   // Update role
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles,delete'); // Delete role
});



Route::prefix('admin/products')->middleware(['auth:user'])->group(function () {
    Route::post('/', [ProductController::class, 'store'])->middleware('permission:products,create');// Create product
    Route::put('{id}', [ProductController::class, 'update'])->middleware('permission:products,update');// Update product
    Route::delete('{id}', [ProductController::class, 'destroy'])->middleware('permission:products,delete');  // Delete product
    Route::post('{parentId}/variants', [ProductController::class, 'storeVariant'])->middleware('permission:products,update'); // Add variant to product
    Route::put('variants/{variantId}', [ProductController::class, 'updateVariant'])->middleware('permission:products,update');// Update variant
    Route::delete('variants/{variantId}', [ProductController::class, 'deleteVariant'])->middleware('permission:products,update');// Delete variant
    Route::post('upload-image', [ProductController::class, 'uploadProductImage'])->middleware('permission:products,update');// Upload image to temp directory
    Route::post('{id}/images', [ProductController::class, 'attachImageToProduct'])->middleware('permission:products,update'); // Attach uploaded image to product
    Route::put('images/{id}', [ProductController::class, 'updateImage'])->middleware('permission:products,update');// Update image
    Route::delete('images/{id}', [ProductController::class, 'destroyImage'])->middleware('permission:products,update');// Delete image
});


Route::prefix('admin/categories')->middleware(['auth:user'])->group(function () {
    Route::post('/', [CategoryController::class, 'store'])->middleware('permission:categories,create'); // Create a new category
    Route::put('{id}', [CategoryController::class, 'update'])->middleware('permission:categories,update'); // Update a category
    Route::delete('{id}', [CategoryController::class, 'destroy'])->middleware('permission:categories,delete'); // Delete a category
    Route::get('{id}/products', [CategoryController::class, 'products'])->middleware('permission:categories,read'); // Get products of a category
});


Route::prefix('admin/category-attributes')->middleware(['auth:user'])->group(function () {

    // Category attributes
    Route::get('/', [CategoryAttributeController::class, 'index'])->middleware('permission:category_attributes,read');
    Route::get('/{id}', [CategoryAttributeController::class, 'show'])->middleware('permission:category_attributes,read');
    Route::post('/', [CategoryAttributeController::class, 'store'])->middleware('permission:category_attributes,create');
    Route::put('/{id}', [CategoryAttributeController::class, 'update'])->middleware('permission:category_attributes,update');
    Route::delete('/{id}', [CategoryAttributeController::class, 'destroy'])->middleware('permission:category_attributes,delete');

    // Attribute values
    Route::get('/{id}/values', [CategoryAttributeController::class, 'getValues'])->middleware('permission:category_attributes,read');
    Route::post('/values', [CategoryAttributeController::class, 'createValue'])->middleware('permission:category_attributes,update');
    Route::post('/values/{id}', [CategoryAttributeController::class, 'updateValue'])->middleware('permission:category_attributes,update');
    Route::delete('/values/{id}', [CategoryAttributeController::class, 'deleteValue'])->middleware('permission:category_attributes,update');
});

//-------------------------------------------------------------------------------------------------

// Shop routes------------------------------------------------------------------


Route::prefix('products')->controller(ShopProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('{id}', 'show');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index'); // Get all root categories
    Route::get('leaf', [CategoryController::class, 'leaf'])->name('leaf'); // Get all leaf categories
    Route::get('hierarchy', [CategoryController::class, 'hierarchy'])->name('hierarchy'); // Get category hierarchy
    Route::get('{id}', [CategoryController::class, 'show'])->name('show'); // Get a specific category
    Route::get('{id}/breadcrumb', [CategoryController::class, 'breadcrumb'])->name('breadcrumb'); // Get breadcrumb for a category
    Route::get('{id}/descendants', [CategoryController::class, 'descendants'])->name('descendants'); // Get descendants of a category
    Route::get('{id}/attributes', [CategoryController::class, 'attributes'])->name('attributes'); // Get attributes of a category
});

use App\Http\Controllers\Shop\CartController;

Route::prefix('cart')->middleware(['auth:customer'])->group(function () {
    Route::get('/', [CartController::class, 'index']); // Get all cart items
    Route::post('/', [CartController::class, 'store']); // Add to cart
    Route::put('{cartId}', [CartController::class, 'update']); // Update cart item quantity
    Route::delete('{cartId}', [CartController::class, 'destroy']); // Remove cart item
    Route::delete('/', [CartController::class, 'clear']); // Clear customer cart
    Route::get('/summary', [CartController::class, 'summary']); // Get cart summary
});

use App\Http\Controllers\Customer\ShipmentController as CustomerShipmentController;

Route::prefix('customer')->middleware(['auth:customer'])->group(function () {
    Route::get('/shipments/{shipmentId}', [CustomerShipmentController::class, 'show']);
    Route::post('/shipments', [CustomerShipmentController::class, 'store']);
    Route::put('/shipments/{shipmentId}', [CustomerShipmentController::class, 'update']);
    Route::get('/carriers', [CustomerShipmentController::class, 'getAllCarriers']);

});

use App\Http\Controllers\Admin\ShipmentController;

Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    // Shipments Routes
    Route::get('/shipments', [ShipmentController::class, 'index'])->middleware('permission:shipments,read');
    Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->middleware('permission:shipments,read');
    Route::post('/shipments', [ShipmentController::class, 'store'])->middleware('permission:shipments,create');
    Route::put('/shipments/{id}', [ShipmentController::class, 'update'])->middleware('permission:shipments,update');
    Route::delete('/shipments/{id}', [ShipmentController::class, 'destroy'])->middleware('permission:shipments,delete');

    // Carriers Routes
    Route::get('/carriers', [ShipmentController::class, 'getAllCarriers'])->middleware('permission:shipments,read');
    Route::get('/carriers/{id}', [ShipmentController::class, 'getCarrierById'])->middleware('permission:shipments,read');
    Route::post('/carriers', [ShipmentController::class, 'storeCarrier'])->middleware('permission:shipments,create');
    Route::put('/carriers/{id}', [ShipmentController::class, 'updateCarrier'])->middleware('permission:shipments,update');
    Route::delete('/carriers/{id}', [ShipmentController::class, 'deleteCarrier'])->middleware('permission:shipments,delete');
});

Route::prefix('customer/orders')->middleware('auth:customer')->group(function () {
    Route::get('/', [CustomerOrderController::class, 'index']);
    Route::post('/', [CustomerOrderController::class, 'store']);
    Route::put('/update-from-cart/{orderId}', [CustomerOrderController::class, 'update']);
});


use App\Http\Controllers\Admin\OrderController;

// Order Routes
Route::prefix('admin/orders')->middleware(['auth:user'])->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index')->middleware('permission:orders,read');
    Route::get('{id}', [OrderController::class, 'show'])->name('show')->middleware('permission:orders,read');
    Route::get('order-number/{orderNumber}', [OrderController::class, 'showByOrderNumber'])->name('showByOrderNumber')->middleware('permission:orders,read');
    Route::post('/', [OrderController::class, 'store'])->name('store')->middleware('permission:orders,create');
    Route::put('{id}', [OrderController::class, 'update'])->name('update')->middleware('permission:orders,update');
    Route::delete('{id}', [OrderController::class, 'destroy'])->name('destroy')->middleware('permission:orders,delete');
    Route::get('customer/{customerId}', [OrderController::class, 'getCustomerOrders'])->name('getCustomerOrders')->middleware('permission:orders,read');
    Route::get('recent/{limit?}', [OrderController::class, 'getRecentOrders'])->name('getRecentOrders')->middleware('permission:orders,read');
    Route::get('expired', [OrderController::class, 'getExpiredOrders'])->name('getExpiredOrders')->middleware('permission:orders,read');
    Route::get('active', [OrderController::class, 'getActiveOrders'])->name('getActiveOrders')->middleware('permission:orders,read');
    Route::post('{id}/paid', [OrderController::class, 'markAsPaid'])->name('markAsPaid')->middleware('permission:orders,update');
    Route::post('{orderId}/items', [OrderController::class, 'createOrderItems'])->name('createOrderItems')->middleware('permission:orders,create');
    Route::delete('{orderId}/items/{itemId}', [OrderController::class, 'deleteOrderItem'])->name('deleteOrderItem')->middleware('permission:orders,delete');
    Route::put('{id}/status', [OrderController::class, 'updateStatus'])->name('updateStatus')->middleware('permission:orders,update');
    Route::put('{id}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('updatePaymentStatus')->middleware('permission:orders,update');
    Route::put('{id}/payment-method', [OrderController::class, 'updatePaymentMethod'])->name('updatePaymentMethod')->middleware('permission:orders,update');
});

Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);

Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    Route::post('/blogs', [BlogController::class, 'store'])->middleware('permission:blogs,create');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->middleware('permission:blogs,update');
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy'])->middleware('permission:blogs,delete');
});

use App\Http\Controllers\Admin\CustomerController;

Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->middleware('permission:customers,read');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware('permission:customers,read');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware('permission:customers,update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('permission:customers,delete');
});

use App\Http\Controllers\Customer\Auth\AuthController as CustomerAuthController;

Route::prefix('customers')->group(function () {
    // Public routes
    Route::post('/ask-otp', [CustomerAuthController::class, 'askOTP']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOTP']);
    Route::post('/login-password', [CustomerAuthController::class, 'loginWithPassword']);

    // Protected routes
    Route::middleware('auth:customer')->group(function () {
        Route::post('/reset-password', [CustomerAuthController::class, 'resetPasswordWithOTP']);
        Route::get('/show-profile', [CustomerAuthController::class, 'showProfile']);
        Route::post('/update-profile', [CustomerAuthController::class, 'updateProfile']);
    });
});

use App\Http\Controllers\Customer\CustomerProductCommentRatingController;

Route::prefix('customer/product-comments')->group(function () {
    Route::get('/{productId}', [CustomerProductCommentRatingController::class, 'index']);
    Route::get('/view/{id}', [CustomerProductCommentRatingController::class, 'show']);
    Route::middleware(['auth:customer'])->group(function () {
        Route::post('/', [CustomerProductCommentRatingController::class, 'store']);
        Route::put('/{id}', [CustomerProductCommentRatingController::class, 'update']);
        Route::delete('/{id}', [CustomerProductCommentRatingController::class, 'destroy']);
        Route::post('/{id}/like', [CustomerProductCommentRatingController::class, 'like']);
        Route::post('/{id}/dislike', [CustomerProductCommentRatingController::class, 'dislike']);
        Route::post('/{id}/unlike', [CustomerProductCommentRatingController::class, 'unlike']);
        Route::post('/{id}/undislike', [CustomerProductCommentRatingController::class, 'undislike']);
    });
});

use App\Http\Controllers\Admin\AdminProductCommentRatingController;


Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    Route::get('product-comments', [AdminProductCommentRatingController::class, 'index'])->middleware('permission:comments,read');
    Route::get('product-comments/{productId}', [AdminProductCommentRatingController::class, 'getByProduct'])->middleware('permission:comments,read');
    Route::get('product-comments/{id}', [AdminProductCommentRatingController::class, 'show'])->middleware('permission:comments,read');
    Route::delete('product-comments/{id}', [AdminProductCommentRatingController::class, 'destroy'])->middleware('permission:comments,delete');
    Route::put('product-comments/{id}/approval', [AdminProductCommentRatingController::class, 'setApprovalStatus'])->middleware('permission:comments,update');
});

use App\Http\Controllers\Customer\CustomerProductConversationController;

Route::prefix('customer')->group(function () {
    Route::get('product-conversations/{productId}', [CustomerProductConversationController::class, 'index']);
    Route::middleware(['auth:customer'])->group(function () {
        Route::post('product-conversations', [CustomerProductConversationController::class, 'store']);
        Route::put('product-conversations/{id}', [CustomerProductConversationController::class, 'update']);
        Route::delete('product-conversations/{id}', [CustomerProductConversationController::class, 'destroy']);
        Route::post('product-conversations/{id}/like', [CustomerProductConversationController::class, 'like']);
        Route::post('product-conversations/{id}/dislike', [CustomerProductConversationController::class, 'dislike']);
        Route::post('product-conversations/{id}/undo-like', [CustomerProductConversationController::class, 'undoLike']);
        Route::post('product-conversations/{id}/undo-dislike', [CustomerProductConversationController::class, 'undoDislike']);
    });
});

use App\Http\Controllers\Admin\AdminProductConversationController;

Route::prefix('admin')->middleware(['auth:user'])->group(function () {
    Route::get('product-conversations', [AdminProductConversationController::class, 'getAllConversations'])->middleware('permission:conversations,read');
    Route::get('product-conversations/{id}', [AdminProductConversationController::class, 'getConversation'])->middleware('permission:conversations,read');
    Route::get('product-conversations/product/{productId}', [AdminProductConversationController::class, 'getConversationsByProduct'])->middleware('permission:conversations,read');
    Route::get('product-conversations/{conversationId}/replies', [AdminProductConversationController::class, 'getReplies'])->middleware('permission:conversations,read');
    Route::delete('product-conversations/{id}', [AdminProductConversationController::class, 'deleteConversation'])->middleware('permission:conversations,delete');
    Route::put('product-conversations/{id}/approval', [AdminProductConversationController::class, 'approveConversation'])->middleware('permission:conversations,update');
    Route::post('product-conversations/{id}/admin-reply', [AdminProductConversationController::class, 'addAdminReply'])->middleware('permission:conversations,create');
    Route::delete('product-conversations/{conversationId}/admin-reply/{replyId}', [AdminProductConversationController::class, 'deleteAdminReply'])->middleware('permission:conversations,delete');
});

