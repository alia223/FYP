<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::all()->where('id', Auth::id());
        foreach($user as $u)  {
            error_log($u);
        }
        return view('settings', compact('user'));
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // form validation
        $user = $this->validate(request(), [
            'user_first_name' => 'required|string',
            'user_last_name' => 'required|string',
            'user_email' => 'required|date',
            'user_telephone' => 'required|string',
            'user_email' => 'required|string'
        ]);
        // create a Booking object and set its values from the input
        $user = User::find($id);
        $user->name = $request->input('user_first_name');
        $user->last_name = $request->input('user_last_name');
        $user->email = $request->input('user_email');
        $user->telephone = $request->input('user_telephone');
        $user->mobile = $request->input('user_mobile');

        // save the Booking object
        $user->save();
        return redirect('settings')->with('success','Settings have been updated');
    }

    
}
