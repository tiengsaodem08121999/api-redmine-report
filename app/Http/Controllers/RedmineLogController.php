<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogTimeRequest;
use Illuminate\Http\Request;
use App\Services\RedmineService;

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

    public function executeReport(Request $request)
    {
        $today = date('Y-m-d');
        $data = $this->redmineService->getUserTasks($today);

        // Filter out daily meeting tasks
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

        $result = $this->redmineService->createDailyReport($data);

        if (isset($result['error'])) {
            return redirect()->route('redmine')->with('error', $result['error']);
        }
        return redirect()->route('redmine')->with('success', 'Báo cáo đã được tạo thành công trên Redmine')
                ->with('report_id', $result['issue']['id']);            
    }

    public function LogTime(LogTimeRequest $request)
    {
       $result = $this->redmineService->logTimeToRedmine($request->all());
        if (isset($result['error'])) {
            return redirect()->route('redmine')->with('error', $result['error']);
        }
        return redirect()->route('redmine')->with('success', 'Log time đã được thực hiện thành công trên Redmine');
    }
}
