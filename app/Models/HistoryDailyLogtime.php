<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryDailyLogtime extends Model
{
    protected $table = 'history_daily_logtimes';
    protected $fillable = ['member', 'task_id', 'spent_time', 'date'];
}
