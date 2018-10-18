<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Project;
use App\Timer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user)
    {

        Project::mine()->findOrFail($project=1)
            ->timers()
            ->save(new Timer([
                'name' => 'Timer',
                'user_id' => Auth::user()->id,
                'started_at' => new Carbon,
            ]));
        return redirect()->route('home');
    }

    public function beforeLogOut(Request $request)
    {
        if ($timer = Timer::mine()->running()->first()) {
            $timer->update(['stopped_at' => new Carbon]);
        }
    }
}
