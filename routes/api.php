<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
example:

await (await fetch("http://127.0.0.1:8000/api/ChatCompletion", {
    method: "POST", 
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
    },
    redirect: "follow",
    referrerPolicy: "no-referrer", 
    body: JSON.stringify({
        model:"blenderbot-400M-distill",
        temperature: 0.7,
        messages:[
            {content: "Hello, whats up?", role:"user"}
            ]
    }), 
})).json()
 */
Route::post('/ChatCompletion', function (Request $request) {
    $request->validate([
        'model' => 'required|in:blenderbot-400M-distill',
        'temperature' => 'required|numeric|min:0.7|max:0.7',
        'messages' => 'required|array|min:1',
        'messages.*.content' => 'required|string',
        'messages.*.role' => 'required|in:user,assistant,system',
    ]);

    $prompt = collect($request->messages)->map(function ($message) {
        return $message["role"] . ': ' . str_replace("\n", " ", $message["content"]);
    })->join("\n");

    $result = shell_exec("/usr/local/python/current/bin/python /workspaces/python-ai-test/main.py " . escapeshellarg($prompt));

    return response()->json([
        'message' => [
            'role' => 'assistant',
            'content' => trim($result),
        ],
    ]);
});
