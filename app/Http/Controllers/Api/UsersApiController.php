<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\Tokens;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;

class UsersApiController extends Controller
{
    protected function login(Request $request){
        try {
            $jsonData = json_decode($request->getContent(), true);
            $user = Users::where("login", "=", $jsonData['login'])->first();
            if (!$user){
                return response("user not found", 404);
            } else {
                if ($user->password !== hash('sha256', $jsonData['password'])){
                    return response("invalid password", 400);
                } else {
                    $token = new Tokens;
                    $token->user_id = $user->id;
                    $token->token = bin2hex(random_bytes(50));
                    $token->save();


                    return response(json_encode([
                        'token' => $token->token,
                        'user' => [
                            'id' => $user->id
                        ]
                    ]), 200);
                }
            }
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function registration(Request $request){
        try {
            $jsonData = json_decode($request->getContent(), true);
            if (Users::where("login", "=", $jsonData['login'])->first()){
                return response("already exists", 400);
            } else {
                $user = new Users;
                $user->login = $jsonData['login'];
                $user->password = hash('sha256', $jsonData['password']);
                $user->save();
                return response("OK", 200);
            }
        } catch (Exception){
            return response("body error", 400);
        }
    }

    protected function get_notifications(Request $request){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        return response(Notifications::where('user_id', '=', $user_id)->get()->toJson(), 200);
    }

    protected function get_notification_by_id(Request $request, int $id){
        $user_id = Utils::authorize($request);
        if (!$user_id) return response("Not authorized", 401);

        $notification = Notifications::find($id);
        if (!$notification) return response("Notification not found", 404);
        return response($notification->toJson(), 200);
    }
}
