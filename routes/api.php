<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\OauthJwtController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request): string {
    return "Welcome base laravel";
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::middleware('oauth_jwt')->get('/profile', function () {
        return auth()->user();
    });
    Route::post('register', [OauthJwtController::class, 'register']);
    Route::post('login', [OauthJwtController::class, 'login']);
    Route::post('refresh', [OauthJwtController::class, 'refresh']);
});

Route::middleware('oauth_jwt')->get('/menus', function () {
    $user = auth()->user();

    $menus = \App\Models\Menu::with(['children' => function ($q) use ($user) {
        $q->where(function ($query) use ($user) {
            $query->whereNull('permission_name')
                ->orWhereIn('permission_name', $user->getAllPermissions()->pluck('name'));
        });
    }])->whereNull('parent_id')
        ->where(function ($query) use ($user) {
            $query->whereNull('permission_name')
                ->orWhereIn('permission_name', $user->getAllPermissions()->pluck('name'));
        })->orderBy('order')->get();

    return $menus;
});

Route::middleware(['oauth_jwt'])->prefix('settings')->group(function () {
    Route::prefix('menus')->group(function () {
        Route::get('', [MenuController::class, 'index']);
        Route::post('', [MenuController::class, 'store']);
        Route::put('/{menu}', [MenuController::class, 'update']);
        Route::delete('/{menu}', [MenuController::class, 'destroy']);
        Route::get('/options', [MenuController::class, 'rolesPermissions']);
    });
});
