<?php 
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pupil;
use App\Models\PupilDietaryRequirement;
use App\Models\User;
use App\Models\BookedPupil;
use App\Models\ActivityLog;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

class PupilDietaryRequirementController extends Controller
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

    }

    public function create(Request $request) {

    }

    public function store(Request $request) {

    }

    public function show($id) {
        $pupil = Pupil::find($id);
        $pupil_dietary_requirements = PupilDietaryRequirement::all()->where('pupil_id', $id);
        return view('pupils.showPupilDietaryRequirements', compact('pupil_dietary_requirements'))->withPupil($pupil);
    }

    public function edit($id) {

    }

    public function update(Request $request, $id) {

    }

    public function destroy($id) {

    }
}
?>