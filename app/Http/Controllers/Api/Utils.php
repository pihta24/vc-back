<?php

namespace App\Http\Controllers\Api;

use App\Models\Tokens;
use Illuminate\Http\Request;

class Utils
{
    public static function authorize(Request $request){
        $auth = $request->headers->get('Authorization', '');
        $user_id = Tokens::where('token', "=", $auth)->first();
        if ($user_id) return $user_id->user_id;
        else return null;
    }
}
