<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return Project::mine()->with('timers')->get()->toArray();
    }

    public function store(Request $request)
    {
        // returns validated data as array
        $data = $request->validate(['name' => 'required|between:2,20']);

        // merge with the current user ID
        $data = array_merge($data, ['user_id' => auth()->user()->id]);

        $project = Project::create($data);

        return $project ? array_merge($project->toArray(), ['timers' => []]) : false;
    }

    public function services(Request $request)
    {

        $project = Project::mine()->get()->first();
        $project = Project::all()->where('user_id','=',auth()->user()->id);

        $hours = $this->workHoursForAProject();
        if ($request->ajax())
            return response()->json(['projects' => $project, 'hours' => $hours]);
    }

    protected function workHoursForAProject()
    {
        $sum = 0;
        $timers = $this->getTimersForAProject();
        foreach ($timers as $timer) {
            $sum += $timer->started_at->diffInSeconds($timer->stopped_at) / (60 * 60);
        }
        return $sum;
    }

    public function getTimersForAProject()
    {
        return Project::mine()
            ->with('timers')->get()->first()
            ->timers()->get();
    }
}
