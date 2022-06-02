<?php

use App\Http\Controllers\GameplayController;
use App\Models\Game;
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

Route::get('/', function () {
    $results = [
        'total' => Game::count(),
        'human' => Game::where('victor', 'x')->count(),
        'computer' => Game::where('victor', 'o')->count(),
        'cat' => Game::where('status', 'complete')->whereNull('victor')->count(),
    ];
    return view('welcome', compact('results'));
});

Route::post('/games/{game}/move', [GameplayController::class, 'move']);
Route::get('/games/new', [GameplayController::class, 'new']);
