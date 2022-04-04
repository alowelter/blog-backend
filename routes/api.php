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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('register', function (Request $request) {
        $credentials = $request->only('name', 'email', 'password');
        $user = \App\Models\User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
        return $user;
    });
    Route::get('logout', function () {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    });
    Route::get('posts/{post}', function (\App\Models\Post $post) {
        return $post;
    });
    Route::post('posts', function (Request $request) {
        $post = \App\Models\Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => str_slug($request->title, "-"),
            'user_id' => auth()->user()->id
        ]);
        return $post;
    });
    Route::put('posts/{post}', function (\App\Models\Post $post, Request $request) {
        $post->update($request->all());
        return $post;
    });
    Route::delete('posts/{post}', function (\App\Models\Post $post) {
        $post = \App\Models\Post::destroy($post->id);
        return response()->json(['message' => 'Post deleted']);
    });


    Route::put('comments/{comment}', function (\App\Models\Comment $comment, Request $request) {
        $comment->update($request->all());
        return $comment;
    });
    Route::delete('comments/{comment}', function (\App\Models\Comment $comment) {
        $comment = \App\Models\Comment::destroy($comment->id);
        return response()->json(['message' => 'Comment deleted']);
    });
    Route::get('comments/{comment}', function (\App\Models\Comment $comment) {
        return $comment;
    });
});

Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $token = auth()->user()->createToken('auth_token')->plainTextToken;
        $user = auth()->user();
        $respon = [
            'status' => 'success',
            'msg' => 'Login successfully',
            'content' => [
                'status_code' => 200,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]
        ];
        return response()->json($respon, 200);
    }
    abort(401);
});

Route::get('posts', function () {
    return \App\Models\Post::all();
});

Route::post('comments', function (Request $request) {
    $comment = \App\Models\Comment::create([
        'post_id' => $request->post_id,
        'visitor_name' => $request->visitor_name,
        'comment' => $request->comment,
    ]);
    return $comment;
});
