<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateProfileRequest;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(){
        return view('users.index')->with('users',User::all());
    }


    public function makeAdmin(User $user){

        $user->role = 'admin';

        $user->save();

        return redirect(route('users.index'));

        session()->flash('success','User Given Admin Privilege');

}


    public function edit(){
        return view('users.edit')->with('user',auth()->user()); // only the authenticated user can edit their profile
    }

    public function update(UpdateProfileRequest $request){

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'about' => $request->about
        ]);

        session()->flash('success','User Profile Updated');

        return redirect(route('users.index'));
    }
}
