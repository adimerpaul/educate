<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller{
    function redirect(){
        return Socialite::driver('google')
            ->scopes(['email','profile'])
            ->stateless()
            ->redirect();
    }
    function callback(){
        $googleUser = Socialite::driver('google')->stateless()->user();
        error_log('Google User: ' . json_encode($googleUser->id));
        $user = User::where('google_id', $googleUser->id)->first();
        if(!$user){
            $user  = new User();
            $user->google_id = $googleUser->id;
            $user->name = $googleUser->name;
            $user->email = $googleUser->email;
            $user->password = bcrypt(bin2hex(random_bytes(16)));
            $user->save();
            $this->downloadAndStoreAvatar($user, $googleUser->getAvatar());
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $urlFrontend = env('FRONTEND_URL', 'http://localhost:9000');
        return redirect("{$urlFrontend}/auth/callback?token={$token}");
    }
    function downloadAndStoreAvatar(User $user, $avatarUrl){
        $avatarContents = file_get_contents($avatarUrl);
        $avatarName = 'avatars/' . $user->id . '.jpg';
//        \Illuminate\Support\Facades\Storage::disk('public')->put($avatarName, $avatarContents);
        Storage::disk('public')->put($avatarName, $avatarContents);
        $user->avatar = '/storage/' . $avatarName;
        $user->save();
    }
}
