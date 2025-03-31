<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RedmineService;

use function PHPUnit\Framework\returnValueMap;

class RedmineLogController extends Controller
{
    protected $redmineService;

    public function __construct(RedmineService $redmineService)
    {
        $this->redmineService = $redmineService;
    }

    public function fetchLogTime()
    {
        $today = request()->get('date') ?? date('Y-m-d');
        $data = $this->redmineService->getUserTasks($today);

        foreach ($data as $key => &$tasks) {
            if (is_array($tasks)) {
                $tasks = array_filter($tasks, function ($task) {
                    return strpos($task['task'], 'Daily_meeting') === false;
                });
            }
            if (empty($tasks)) {
                unset($data[$key]);
            }
        }

        return view('redmine', compact('data'));
    }

    public function fetchTimeSheet(Request $request) {
        $date = $request->get('date');
        $projectId = $request->get('project_id');   
        $dailyReportListWithMonth = $this->redmineService->getDailyReports($date, $projectId);
        $project = $this->redmineService->getProject();
        $userNotLogTime = $this->redmineService->getUserNotLogTime($dailyReportListWithMonth);
        return view('timesheet', compact('dailyReportListWithMonth', 'project', 'userNotLogTime'));
    }
}
