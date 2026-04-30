<?php

namespace App\Http\Controllers;

use App\Agents\EduHelperAgent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $sessionKey = $request->session()->getId();

        $agent = new EduHelperAgent($sessionKey);

        $response = $agent->respond($request->message);

        return response()->json(['reply' => $response]);
    }
}
