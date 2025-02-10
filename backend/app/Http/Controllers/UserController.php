<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(UserRequest $request)
    {
        return User::create($request->validated());
    }

    public function edit(UserRequest $request, User $user)
    {
        $user->update($request->validated());

        return response()->json([
            'message' => 'Użytkownik został pomyślnie zaktualizowany.',
            'user' => $user
        ], 200);
    }

    public function delete(User $user) 
    {
        $user->delete();
        return response()->json(['message' => 'Pracownik usunięty', 200]);
    }
}
