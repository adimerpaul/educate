<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Devuelve el perfil del usuario autenticado (o lo crea vacío)
    public function me(Request $request)
    {
        $user = $request->user();

        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'username' => $user->username,
                'nombre'   => $user->name,
            ]
        );

        return $profile;
    }

    // Actualiza/crea el perfil del usuario autenticado
    public function save(Request $request)
    {
        $user = $request->user();

        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        // SIN validación, tal como pediste
        $profile->fill($request->all());
        $profile->user_id = $user->id;
        $profile->save();

        return $profile;
    }
}
