<?php

namespace App\Http\Controllers;


use App\Project;
use Flow\Config;
use Flow\File;
use Illuminate\Support\Facades\Input;


class HomeController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invoice()
    {
        return view('management.invoice');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statisticalService()
    {
        $this->peakTime();
        return view('logs.index');
    }

    /**
     * @param int $day
     * @param int $project
     */
    public function peakTime($day = 1, int $project = 1)
    {
        $timers = Project::where('id', '=', $project)->with('timers')->get()->first()->timers()->get();
        $this->sharedTimePointsScanner($timers);

    }

    /**
     * @param $timers
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function sharedTimePointsScanner($timers)
    {
        $numberCrossedTimer = [];

        foreach ($timers as $timer1) {
            $number = 0;
            foreach ($timers as $timer2) {
                if ($timer2->isContain($timer1->started_at)) {
                    $number += 1;
                }
            }
            array_push($numberCrossedTimer, [$number, $timer1]);
        }
        $mostVisit = $numberCrossedTimer[0];
        foreach ($numberCrossedTimer as $item) {
            $mostVisit = max($item[1], $mostVisit);
        }
        dd($mostVisit);
        return view('management.peakTime', compact('mostVisit'));

    }




}
