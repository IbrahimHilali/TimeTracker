<?php

namespace App\Http\Controllers;


use App\Project;
use Flow\Config;
use Flow\File;


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

    public function invoice()
    {
        return view('management.invoice');
    }

    public function statisticalService()
    {
        $this->peakTime();
        return view('logs.index');
    }

    public function peakTime($day = 1, int $project = 1)
    {
        $timers = Project::where('id', '=', $project)->with('timers')->get()->first()->timers()->get();
        $this->sharedTimePointsScanner($timers);

    }

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


    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getFile()
    {

        $file = $this->initFlowFile();

        if ($file->checkChunk()) {
            return response("Getting File");
        } else {
            return response("No Content", 204);
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function postFile()
    {
        $file = $this->initFlowFile();

        if ($file->validateChunk()) {
            $file->saveChunk();
        } else {
            return response("Bad Request", 400);
        }
    }

    /**
     * @return File
     */
    protected function initFlowFile()
    {
        $config = new Config();
        if (!is_dir(storage_path() . '/uploads/temp/')) {
            mkdir(storage_path() . '/uploads/temp/', 0775, true);
        }

        $config->setTempDir(storage_path() . '/uploads/temp/');

        return new File($config);
    }


}
