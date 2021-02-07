<?php

use App\Http\Controllers\ZohoCRMController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ZohoCRMController::class, 'index']);
Route::get('create-deal', [ZohoCRMController::class, 'create_deal']);
Route::get('create-deal-self-client', [ZohoCRMController::class, 'create_deal_self_client']);
