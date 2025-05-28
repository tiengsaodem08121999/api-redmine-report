<?php

namespace App\Http\Controllers;

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

        return view('report', compact('data'));
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
        $result = $this->redmineService->createDailyReport($data , $request->all());

        if (isset($result['error'])) {
            return redirect()->route('report')->with('error', $result['error']);
        }
        return redirect()->route('report')->with('success', 'Báo cáo đã được tạo thành công trên Redmine')
                ->with('report_id', $result['issue']['id']);            
    }

    public function LogTime(Request $request)
    {
       $result = $this->redmineService->logTimeToRedmine($request->all());
        if (isset($result['error'])) {
            return redirect()->route('report')->with('error', $result['error']);
        }
        return redirect()->route('report')->with('success', 'Log time đã được thực hiện thành công trên Redmine');
    }

    public function createTask()
    {
        return view('create_task');
    }

    public function executeCreateTask(Request $request) 
    {
        $data = $this->formatDataCreateTask($request->all());
        $result = $this->redmineService->createTasks($data);
        if (isset($result['error'])) {
            return redirect()->route('create_task')->with('error', $result['error']);
        }
        return redirect()->route('create_task')->with('success', 'Task đã được tạo thành công trên Redmine');
    }

    public function formatDataCreateTask($data)
    {
        $tasks = [];
        $count = count($data['subject']);
        
        for ($i = 0; $i < $count; $i++) {

            $subTask = $data['sub_task'][$i];
    
            if (is_numeric($subTask)) {
                $subTask = (int) $subTask;
            }

            $tasks[] = [
                'tracker' => $data['tracker'][$i],
                'subject' => $data['subject'][$i],
                'description' => $data['description'][$i],
                'sub_task' => $subTask,
                'assignee' => $data['assignee'][$i],
                ];
        }
        return $tasks;
    }

    public function checkLogtime()
    {
        $data = $this->redmineService->checkLogtimeForThisMonth();
        return view('check_logtime', compact('data'));
    }

    public function logtimeForThisMonth()
    {
        $workingThisMonth = $this->redmineService->getWorkingDaysOfThisMonth();
        return view('logtime_for_this_month', compact('workingThisMonth'));
    }

    public function executeLogtimeForThisMonth(Request $request)
    {
         $data = $this->redmineService->executeLogtimeForThisMonth($request->except('_token'));
        if (isset($data['error'])) {
            return redirect()->route('logtime_for_this_month')->with('error', $data['error'])->with('taskErrors', $data['taskErrors']);
        }
        return redirect()->route('logtime_for_this_month')->with('taskSuccess',  $data['taskSuccess'] )->with('taskErrors', $data['taskErrors']);
    }
}
