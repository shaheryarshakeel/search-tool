<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('index', [\App\Http\Controllers\MemberController::class, 'getAll']);
Route::get('getMember/{id}', [\App\Http\Controllers\MemberController::class, 'getMember']);
Route::post('createMember', [\App\Http\Controllers\MemberController::class, 'create']);
Route::post('addAsFriend', [\App\Http\Controllers\FriendController::class, 'addAsFriend']);
Route::post('search', [\App\Http\Controllers\SearchController::class, 'search']);
