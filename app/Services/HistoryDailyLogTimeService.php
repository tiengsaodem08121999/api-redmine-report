<?php

namespace App\Services;

use App\Models\HistoryDailyLogtime;

class HistoryDailyLogTimeService
{
    public function getHistoryDailyLogtime($data)
    {
        try {
            foreach ($data as $member => $tasks) {
                foreach ($tasks as $key => $task) {
                    preg_match('/#(\d+)/', $task['task'], $matches);
                    $history_daily_logtime_for_member  = [
                        'member' => $member,
                        'task_id' => $matches[1],
                        'spent_time' => $task['spent_time'],
                        'date' => date('Y-m-d'),
                    ];
                    if (HistoryDailyLogtime::where('task_id', $matches[1])->where('date', date('Y-m-d'))->exists()) {
                        $history_daily_logtime = HistoryDailyLogtime::where('task_id', $matches[1])->where('date', date('Y-m-d'))->first();
                        $history_daily_logtime->spent_time = $task['spent_time'];
                        $history_daily_logtime->save();
                        continue;
                    }
                    $history_daily_logtime = HistoryDailyLogtime::create($history_daily_logtime_for_member);
                    if (!$history_daily_logtime) {
                        throw new \Exception('Failed to create history daily logtime');
                    }
                }
            }
            return [
                'success' => true,
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}