<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\ShareKeys;
use App\Models\Tasks;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;

class TasksApiController extends Controller
{
    protected function create_task(Request $request){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        try {
            $jsonData = json_decode($request->getContent(), true);
            $task = new Tasks;
            $task->creator = $user_id;
            $task->owner = $user_id;
            $task->spectators = '';
            $task->text = $jsonData['text'];
            $task->time = 0;
            $task->title = $jsonData['title'];
            $task->completed = false;
            $task->save();

            return response($task->toJson(), 200);
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function get_tasks(Request $request){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        return response(Tasks::where('creator', '=', $user_id, 'or')
            ->where('owner', '=', $user_id, 'or')->get()->toJson(), 200);
    }

    protected function get_task_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $auth = $request->headers->get('share_key', '');
        $task_id = ShareKeys::where('share_key', "=", $auth)->first();

        if ($task_id){
            if ($task_id->task != $id) return response('Not authorized', 401);
            if ((time() < $task_id->expires or $task_id->expires == -1) and $task_id->visitors != 0){
                if ($task_id->visitors > 0) {
                    $task_id->visitors -= 1;
                    $task_id->save();
                }
                return response(Tasks::find($task_id->task)->toJson());
            } else {
                $task_id->delete();
                return response("share key expired", 401);
            }
        } else {
            $task = Tasks::find($id);
            if (!$task) return response('Not found', 404);
            if ($user_id == $task->creator or $user_id == $task->owner or str_contains($task->spectators, $user_id)){
                return response($task->toJson(), 200);
            } else return response('Not authorized', 401);
        }
    }

    protected function update_task_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $auth = $request->headers->get('share_key', '');
        $task_id = ShareKeys::where('share_key', "=", $auth)->first();

        if ($task_id){
            if ($task_id->task != $id) return response('Not authorized', 401);
            if ((time() < $task_id->expires or $task_id->expires == -1) and $task_id->visitors != 0){
                if ($task_id->can_edit) {
                    try {
                        $jsonData = json_decode($request->getContent(), true);
                        $task = Tasks::find($task_id->task);
                        if (array_key_exists('text', $jsonData)) $task->text = $jsonData['text'];
                        if (array_key_exists('title', $jsonData)) $task->title = $jsonData['title'];
                        $task->save();

                        $notification = new Notifications;
                        $notification->user_id = $task->creator;
                        $notification->text = $task->title + 'was changed.';
                        $notification->save();
                        return response("OK", 200);
                    } catch (Exception){
                        return response("body error", 400);
                    }
                } else {
                    return response("Not authorized", 401);
                }
            } else {
                $task_id->delete();
                return response("share key expired", 401);
            }
        } else {
            $task = Tasks::find($id);
            if (!$task) return response('Not found', 404);
            if ($user_id == $task->creator){
                try {
                    $jsonData = json_decode($request->getContent(), true);
                    if (array_key_exists('text', $jsonData)) $task->text = $jsonData['text'];
                    if (array_key_exists('title', $jsonData)) $task->title = $jsonData['title'];
                    $task->save();

                    $notification = new Notifications;
                    $notification->user_id = $task->creator;
                    $notification->text = $task->title + 'was changed.';
                    $notification->save();
                    return response("OK", 200);
                } catch (Exception) {
                    return response("body error", 400);
                }
            } else return response('Not authorized', 401);
        }
    }

    protected function delete_task_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            $task->delete();
            $notification = new Notifications;
            $notification->user_id = $task->creator;
            $notification->text = $task->title + 'was deleted.';
            $notification->save();
            return response("OK", 200);
        } else return response('Not authorized', 401);
    }

    protected function mark_completed_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            $task->completed = true;
            $notification = new Notifications;
            $notification->user_id = $task->creator;
            $notification->text = $task->title + 'marked completed.';
            $notification->save();
            return response("OK", 200);
        } else return response('Not authorized', 401);
    }

    protected function delete_completed_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator or $user_id == $task->owner){
            $task->completed = false;
            $notification = new Notifications;
            $notification->user_id = $task->creator;
            $notification->text = $task->title + 'mark completed deleted.';
            $notification->save();
            return response("OK", 200);
        } else return response('Not authorized', 401);
    }

    protected function share_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            try {
                $jsonData = json_decode($request->getContent(), true);
                $s_key = new ShareKeys;
                $s_key->task = $task->id;
                $s_key->expires = (array_key_exists('time', $jsonData)) ? time() + $jsonData['time']:-1;
                $s_key->visitors = (array_key_exists('maxVisitors', $jsonData)) ? $jsonData['maxVisitors']:-1;
                $s_key->can_edit = (array_key_exists('canEdit', $jsonData)) ? $jsonData['canEdit']:false;
                $s_key->save();
                return response("OK", 200);
            } catch (Exception) {
                return response("body error", 400);
            }
        } else return response('Not authorized', 401);
    }

    protected function set_owner_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            try {
                $jsonData = json_decode($request->getContent(), true);
                $user = Users::find($jsonData['id']);
                if (!$user) return response("User not found", 404);
                $task->owner = $jsonData['id'];
                $task->save();
                return response("OK", 200);
            } catch (Exception) {
                return response("body error", 400);
            }
        } else return response('Not authorized', 401);
    }

    protected function get_owner_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator or $user_id == $task->owner or str_contains($task->spectators, $user_id)){
            return response(['id'=>$task->owner], 200);
        } else return response('Not authorized', 401);
    }

    protected function delete_owner_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            $task->owner = '';
            $task->save();
            return response("OK", 200);
        } else return response('Not authorized', 401);
    }

    protected function add_spectator_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            try {
                $jsonData = json_decode($request->getContent(), true);
                if (array_key_exists('id', $jsonData)) {
                    $user = Users::find($jsonData['id']);
                    if (!$user) return response("User not found", 404);
                    $task->spectators += $jsonData['id']+";";
                }
                if (array_key_exists('ids', $jsonData)) {
                    foreach ($jsonData['ids'] as $id) {
                        $user = Users::find($jsonData['id']);
                        if (!$user) return response("User not found", 404);
                        $task->spectators += $jsonData['id']+";";
                    }
                }
                $task->save();
                return response("OK", 200);
            } catch (Exception) {
                return response("body error", 400);
            }
        } else return response('Not authorized', 401);
    }

    protected function delete_spectator_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);
    }

    protected function get_spectator_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);
    }

    protected function set_time_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            try {
                $jsonData = json_decode($request->getContent(), true);
                $task->time = $jsonData['time_end'];
                $task->save();
                return response("OK", 200);
            } catch (Exception) {
                return response("body error", 400);
            }
        } else return response('Not authorized', 401);
    }

    protected function get_time_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator or $user_id == $task->owner or str_contains($task->spectators, $user_id)){
            return response(['time_end'=>$task->time], 200);
        } else return response('Not authorized', 401);
    }

    protected function delete_time_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $task = Tasks::find($id);
        if (!$task) return response('Not found', 404);
        if ($user_id == $task->creator){
            $task->time = -1;
            $task->save();
        } else return response('Not authorized', 401);
    }
}
