<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FirebaseAccessTokenService;
use App\Services\FirebaseMessangingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FirebaseController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $service = new FirebaseAccessTokenService();
        $accesstoken = $service->getAccessToken();
        $firebase = new FirebaseMessangingService();
        $firebase->sendData($accesstoken, $request->title, $request->body);
        return response()->json(['message' => 'Notification sent'], 200);
    }
}
