<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boards;
use App\Models\BoardsAccess;
use App\Models\Tasks;
use Exception;
use Illuminate\Http\Request;

class BoardsApiController extends Controller
{
    private function convert_access(array $access){
        $users = [];
        foreach ($access as $a){
            $users[$a->user_id] = $a->access;
        }

        return $users;
    }

    private function convert_tasks(array $tasks){
        $task = [];
        foreach ($tasks as $t){
             $task_info = Tasks::find($t);
             $task[] = $task_info->toJson();
        }

        return $task;
    }

    protected function create_board(Request $request){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        try {
            $jsonData = json_decode($request->getContent(), true);
            $board = new Boards;
            $board->title = $jsonData['title'];
            $board->tasks = "";
            $board->save();

            $baccess = new BoardsAccess;
            $baccess->user_id = $user_id;
            $baccess->board_id = $board->id;
            $baccess->access = "full";
            $baccess->save();

            $resp = $board->toArray();
            $resp['users'] = [$user_id => "full"];
            $resp['tasks'] = [];

            return response($resp, 200);
        } catch (Exception){
            return response("body error", 400);
        }

    }

    protected function get_boards(Request $request){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $resp = [];

        $access = BoardsAccess::where('user_id', '=', $user_id)->get();
        foreach ($access as $a) {
            $board = Boards::find($a->board_id)->toArray();
            $board['users'] = $this->convert_access(BoardsAccess::where('board_id', '=', $board['id'])->toArray());
            $board['tasks'] = $this->convert_tasks(preg_split(';', $board['tasks']));
            $resp[] = $board;
        }

        return response(json_encode($resp), 200);
    }

    protected function get_board_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)->where('board_id', '=', $id)->first();
        if (!$access) return response("Not authorized");

        $board = Boards::find($id)->toArray();
        $board['users'] = $this->convert_access(BoardsAccess::where('board_id', '=', $board['id'])->toArray());
        $board['tasks'] = $this->convert_tasks(preg_split(';', $board['tasks']));

        return response(json_encode($board), 200);
    }

    protected function delete_board_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->where('access', '=', 'full')->first();
        if (!$access) return response("Not authorized");

        Boards::find($id)->delete();
        BoardsAccess::where('board_id', '=', $id)->delete();
        return response("OK", 200);
    }

    protected function get_board_tasks_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)->where('board_id', '=', $id)->first();
        if (!$access) return response("Not authorized");

        return response(json_encode($this->convert_tasks(preg_split(';', Boards::find($id)->tasks))), 200);
    }

    protected function put_task_on_board_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->whereIn('access', ['write', 'full'])->first();
        if (!$access) return response("Not authorized");

        try {
            $jsonData = json_decode($request->getContent(), true);
            $board = Boards::find($id);
            $board->tasks += $jsonData['id'] + ";";
            $board->save();
            return response("OK", 200);
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function remove_task_from_board_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->whereIn('access', ['write', 'full'])->first();
        if (!$access) return response("Not authorized");

        try {
            $jsonData = json_decode($request->getContent(), true);
            $board = Boards::find($id);
            str_replace($jsonData['id'] + ";", "",$board->tasks, );
            $board->save();
            return response("OK", 200);
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function add_access_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->where('access', '=', 'full')->first();
        if (!$access) return response("Not authorized");

        try {
            $jsonData = json_decode($request->getContent(), true);
            $baccess = new BoardsAccess;
            $baccess->access = $jsonData['access'];
            $baccess->board_id = $id;
            $baccess->user_id = $jsonData['id'];
            $baccess->save();
            return response("OK", 200);
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function get_access_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->first();
        if (!$access) return response("Not authorized");

        return response(json_encode($this->convert_access(BoardsAccess::where('board_id', '=', $id)->toArray())), 200);
    }

    protected function delete_access_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $access = BoardsAccess::where('user_id', '=', $user_id)
            ->where('board_id', '=', $id)->where('access', '=', 'full')->first();
        if (!$access) return response("Not authorized");

        try {
            $jsonData = json_decode($request->getContent(), true);
            BoardsAccess::where('board_id', '=', $id)->where('user_id', '=', $jsonData['id'])->delete();
            return response("OK", 200);
        } catch (Exception){
            return response("body error", 400);
        }
    }
}
