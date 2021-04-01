<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backend\UsersController as BackendUsersController;
use App\Http\Controllers\Backend\RolesController as BackendRolesController;
use App\Http\Controllers\Backend\MenusController as BackendMenusController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

Route::get('backend', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('backend', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/quick-search', [PagesController::class, 'quicksearch'])->name('quick-search');

Route::prefix('backend')->name('backend.')->middleware('auth:web')->group(function () {
  Route::group(['middleware' => ['role:super-admin|admin']], function () {
    Route::resource('users', BackendUsersController::class)->except('show');
    Route::resource('roles', BackendRolesController::class)->except(['create', 'show', 'destroy']);
    Route::prefix('menus')->name('menus.')->group(function() {
      Route::post('change_hierarchy', [BackendMenusController::class,'change_hierarchy'])->name('change_hierarchy');
      Route::post('autocomplete ', [BackendMenusController::class,'autocomplete '])->name('autocomplete');
    });
    Route::resource('menus', BackendMenusController::class)->except(['create', 'show']);
  });
});

Route::get('/test', function (Request $request) {
    $spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello_world.xlsx');
});
