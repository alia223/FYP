<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class RuleController extends Controller
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

    public function index() {
        return view('admin.controlPanel');
    }

    public function store(Request $request) {
                //
        // form validation
        $rules = $this->validate(request(), [
            'brand_colour' => 'required|string',
            'text_colour' => 'required|string',
            'club_start' => 'required|String',
            'club_end' => 'required|string',
            'club_duration_step' => 'required|integer',
            'brand_logo' => 'mimes:jpeg,bmp,png',
            'pupil_ratio' => 'required|integer',
            'pupil_min_age' => 'required|integer',
            'pupil_max_age' => 'required|integer'
        ]);

        $rules = Rule::all()->first();
        $rules->brand_colour = $request->input('brand_colour');
        $rules->text_colour = $request->input('text_colour');
        $rules->club_start = date('H:i:s', strtotime($request->input('club_start')));
        $rules->club_end = date('H:i:s', strtotime($request->input('club_end')));
        $rules->club_duration_step = $request->input('club_duration_step');
        $rules->pupil_ratio = $request->input('pupil_ratio');
        $rules->pupil_min_age = $request->input('pupil_min_age');
        $rules->pupil_max_age = $request->input('pupil_max_age');
        if($request->hasFile('brand_logo')) {
            $request->file('brand_logo')->store('images', 'public');
            $brand_logo = $request->file('brand_logo')->hashName();
            $rules->brand_logo = $brand_logo;
        }
        $rules->save();
        $this->log_activity("Rules in control panel updated");
        return view('admin.controlPanel');
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}
