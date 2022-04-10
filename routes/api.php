<?php

use App\Http\Controllers\Api\BoardsApiController;
use App\Http\Controllers\Api\TasksApiController;
use App\Http\Controllers\Api\UsersApiController;
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
Route::post('login', [UsersApiController::class, 'login']);
Route::post('registration', [UsersApiController::class, 'registration']);

Route::get('task', [TasksApiController::class, 'get_tasks']);
Route::post('task', [TasksApiController::class, 'create_task']);
Route::get('task/{id}', [TasksApiController::class, 'get_task_by_id']);
Route::put('task/{id}', [TasksApiController::class, 'update_task_by_id']);
Route::delete('task/{id}', [TasksApiController::class, 'delete_task_by_id']);
Route::put('task/{id}/complete', [TasksApiController::class, 'mark_completed_by_id']);
Route::delete('task/{id}/complete', [TasksApiController::class, 'delete_completed_by_id']);
Route::put('task/{id}/share', [TasksApiController::class, 'share_by_id']);
Route::put('task/{id}/owner', [TasksApiController::class, 'set_owner_by_id']);
Route::delete('task/{id}/owner', [TasksApiController::class, 'delete_owner_by_id']);
Route::get('task/{id}/owner', [TasksApiController::class, 'get_owner_by_id']);
Route::put('task/{id}/spectator', [TasksApiController::class, 'add_spectator_by_id']);
Route::delete('task/{id}/spectator', [TasksApiController::class, 'delete_spectator_by_id']);
Route::get('task/{id}/spectator', [TasksApiController::class, 'get_spectator_by_id']);
Route::get('task/{id}/time', [TasksApiController::class, 'get_time_by_id']);
Route::put('task/{id}/time', [TasksApiController::class, 'set_time_by_id']);
Route::delete('task/{id}/time', [TasksApiController::class, 'delete_time_by_id']);

Route::get('notification', [UsersApiController::class, 'get_notifications']);
Route::get('notification/{id}', [UsersApiController::class, 'get_notification_by_id']);

Route::post('board', [BoardsApiController::class, 'create_board']);
Route::get('board', [BoardsApiController::class, 'get_boards']);
Route::get('board/{id}', [BoardsApiController::class, 'get_board_by_id']);
Route::delete('board/{id}', [BoardsApiController::class, 'delete_board_by_id']);
Route::get('board/{id}/tasks', [BoardsApiController::class, 'get_board_tasks_by_id']);
Route::put('board/{id}/tasks', [BoardsApiController::class, 'put_task_on_board_by_id']);
Route::delete('board/{id}/tasks', [BoardsApiController::class, 'remove_task_from_board_by_id']);
Route::put('board/{id}/access', [BoardsApiController::class, 'add_access_by_id']);
Route::get('board/{id}/access', [BoardsApiController::class, 'get_access_by_id']);
Route::delete('board/{id}/access', [BoardsApiController::class, 'delete_access_by_id']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
