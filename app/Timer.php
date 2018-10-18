<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MongoDB\BSON\Timestamp;

class Timer extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name', 'user_id', 'project_id', 'stopped_at', 'started_at'
    ];

    /**
     * {@inheritDoc}
     */
    protected $dates = ['started_at', 'stopped_at'];

    /**
     * {@inheritDoc}
     */
    protected $with = ['user'];

    /**
     * Get the related user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get timer for current user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMine($query)
    {
        return $query->whereUserId(auth()->user()->id);
    }

    /**
     * Get the running timers
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRunning($query)
    {
        return $query->whereNull('stopped_at');
    }

    /**
     * @param Carbon $pointTime
     * @return bool
     */
    public function isContain(Carbon $pointTime){

        if($pointTime->between($this->started_at,$this->stopped_at)){
            return true;
        }
        return false;
    }

    /**
     * @param Timer $timer
     * @return bool
     */
    public function isContainTimer(Timer $timer){

        if($this->isContain($timer->started_at) and $this->isContain($timer->stopped_at)){
            return true;
        }
        return false;
    }

}
