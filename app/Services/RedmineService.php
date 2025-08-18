<?php

namespace App\Services;

use App\Models\HistoryDailyLogtime;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;

class RedmineService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;
    protected $project;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = env('REDMINE_API_URL') ?? 'https://tools.splus-software.com/redmine';
        $this->apiKey = env('REDMINE_API_KEY') ?? 'cac020bc0f405ab33aba64abffe5216beacf4a27';
        $this->project = env('REDMINE_PROJECT') ?? 's7-ec-cube';
    }

    /**
     * Gọi API Redmine để lấy danh sách log time theo ngày
     */
    public function fetchTimeEntries($date)
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}/time_entries.json", [
                'query' => [
                    'spent_on' => $date,
                    'key' => $this->apiKey
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Redmine API Error: " . $e->getMessage());
            return ['error' => 'Không thể lấy dữ liệu từ Redmine'];
        }
    }

    /**
     * Gọi API để lấy chi tiết task theo ID
     */
    public function fetchTaskDetail($taskId)
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}/issues/{$taskId}.json", [
                'query' => ['key' => $this->apiKey]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            $subject = $data['issue']['subject'] ?? 'Không có tiêu đề';
            $tracker = $data['issue']['tracker']['name'] ?? 'Không có tracker';
            $status = $data['issue']['status']['name'] ?? 'Không có status';

            return [
                'id' => $taskId,
                'subject' => $subject,
                'tracker' => $tracker,
                'status' => $status
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy thông tin task: " . $e->getMessage());
            return "Không thể lấy task";
        }
    }

    /**
     * Lấy danh sách user đã log time trong ngày và nhóm theo user
     */
    public function getUserTasks($date)
    {
        $data = $this->fetchTimeEntries($date);

        if (isset($data['error'])) {
            return $data;
        }

        $groupedTasks = [];

        foreach ($data['time_entries'] as $entry) {
            $user = $entry['user']['name'];
            $taskId = $entry['issue']['id'];
            $taskName = $this->fetchTaskDetail($taskId);
            $taskFormatted = $taskName['tracker'] . ' #' . $taskName['id'] . ' :'  . $taskName['subject'] . ' ';
            $groupedTasks[$user][] = [
                'id' => $entry['id'],
                'task' => $taskFormatted,
                'status' => $taskName['status'],
                'spent_time' => $entry['hours'],
            ];
        }
        return $groupedTasks;
    }

    public function executeReport($request)
    {
        $date = $request['date'];
        $data = $this->getUserTasks($date);
        return $data;
    }

    public function createDailyReport($data, $request)
    {
        try {
            $today = date('Y-m-d');
            $subject = '日報　' . date('Y年n月j日');

            // Format the description
            // $description = "*1.【定量報告】*\n\n";

            // // Front section
            // $description .= "* Front\n";
            // $description .= "** CR/Overlooked: Done/Total: {$request['cr_font']}\n";
            // $description .= "** Bug: Fixed/Total: {$request['bug_font']}\n";
            // $description .= "** Pending: 1\n\n";

            // // EC section
            // $description .= "* EC\n";
            // $description .= "** CR/Overlooked: Done/Total: {$request['cr_cms']}\n";
            // $description .= "** Bug: Fixed/Total: {$request['bug_cms']}\n\n";

            // // API section
            // $description .= "* API\n";
            // $description .= "** CR/Overlooked: Done/Total: {$request['cr_api']}\n";
            // $description .= "** Bug: Fixed/Total: {$request['bug_api']}\n\n";

            // Today's tasks section
            $description = "*1.【本日のタスク】*\n\n";
            $description .= $this->formatTasksTable($data) . "\n\n";

            // Notices section
            $description .= "*2.【連絡事項】*\n\n";
            $description .= "* 「MR Status」列でステータスが「Merged」となっている「Issue」について、正確に対応済みかどうかご確認のほどよろしくお願いします。\n";
            $description .= "\"EC-Admin - UAT - IssuesList - レビュー - Google Sheets\":https://docs.google.com/spreadsheets/d/1ey0D5r4XX3mmtOP5EaHts2M09fcoDj9nKegqkfCne0s/edit?gid=0#gid=0\n";
            $response = $this->client->request('POST', "{$this->apiUrl}/issues.json", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Redmine-API-Key' => $this->apiKey
                ],
                'json' => [
                    'issue' => [
                        'project_id' => $this->project,
                        'subject' => $subject,
                        'status_id' => 1, // New status
                        'tracker_id' => 8, // Report
                        'start_date' => $today,
                        'due_date' => $today,
                        'description' => $description,
                        'assigned_to_id' => 758 // DuongNT
                    ]
                ]
            ]);
            if ($response->getStatusCode() !== 201) {
                return ['error' => 'Không thể tạo báo cáo trên Redmine'];
            }
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Redmine API Error: " . $e->getMessage());
            return ['error' => 'Không thể tạo báo cáo trên Redmine'];
        }
    }

    private function formatTasksTable($data)
    {
        $table = "|_. # |_. 開発者 |_. ID タスク |_. ステータス |_. 備考 |\n";
        $index = 1;
        $splus = 'Splus.';
        $developers = config('information.developer_report');

        foreach ($developers as $dev) {
            $taskContents = [];
            $taskStatuses = [];

            if (isset($data[$dev])) {
                foreach ($data[$dev] as $task) {
                    // Task content
                    $taskContent = is_array($task['task']) ? implode("\n", $task['task']) : $task['task'];
                    $taskContents[] = $taskContent;

                    // Status
                    $status = is_array($task['status']) ? implode("\n", $task['status']) : $task['status'];
                    $taskStatus = $status == 'Closed' || $status == 'Resolved' ? '完了' : '進行中';
                    $taskStatuses[] = $taskStatus;
                }
            }

            $taskColumn = implode("\n", $taskContents);
            $statusColumn = implode("\n", $taskStatuses);

            $table .= "| {$index} |{$splus}{$dev}| {$taskColumn} | {$statusColumn}|. |\n";
            $index++;
        }

        return $table;
    }

    public function logTimeToRedmine(array $data)
    {
        $redmineUrl = $this->apiUrl;
        $apiKey =  $data['key'];
        $result = [];

        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("$redmineUrl/time_entries.json", [
            'time_entry' => [
                'issue_id'    => (int) $data['task_id'],
                'hours'       => $data['spent_time'],
                'spent_on'    => Carbon::parse($data['date'])->format('Y-m-d'),
                'activity_id' => $data['activity_id'],
            ]
        ]); 

        if ($response->failed()) {
            $result = [
                'error' => 'Failed to log time on Redmine: ' . $response->body(),
            ];
            return $result;
        }
        return $response->json();
    }

    public function createTasks(array $data)
    {
        $redmineUrl = $this->apiUrl;
        $apiKey = $this->apiKey;
        $subTask = [];
        foreach ($data as $key => $task) {
            if ($task['sub_task'] !== null) {
                $subTask[] = $task;
                continue;
            }
            $response = Http::withHeaders([
                'X-Redmine-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("$redmineUrl/issues.json", [
                'issue' => [
                    'project_id' => $this->project,
                    'subject' => $task['subject'],
                    'tracker_id' => $task['tracker'],
                    'description' => $task['description'],
                    'status_id' => 2,
                    'assigned_to_id' => $task['assignee'],
                    'due_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'custom_fields' => [
                        [
                            'id' => 1,
                            'value' => Carbon::now()->format('Y-m-d')
                        ],
                        [
                            'id' => 2,
                            'value' => Carbon::now()->addDays(7)->format('Y-m-d')
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to create tasks on Redmine: ' . $response->body());
            }
        }
        foreach ($subTask as $key => $task) {

            if (is_numeric($task['sub_task'])) {
                $subtask_id = $task['sub_task'];
            } else {
                $subtask_id = $this->getTaskforSubject($task['sub_task']);
            }

            $response = Http::withHeaders([
                'X-Redmine-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("$redmineUrl/issues.json", [
                'issue' => [
                    'project_id' => $this->project,
                    'subject' => $task['subject'],
                    'tracker_id' => $task['tracker'],
                    'description' => $task['description'],
                    'assigned_to_id' => $task['assignee'],
                    'parent_issue_id' => $subtask_id,
                    'done_ratio' => 10,
                    'status_id' => 2,
                    'due_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'custom_fields' => [
                        [
                            'id' => 1,
                            'value' => Carbon::now()->format('Y-m-d')
                        ],
                        [
                            'id' => 2,
                            'value' => Carbon::now()->addDays(7)->format('Y-m-d')
                        ]
                    ]
                ]
            ]);
        }
        return $response->json();
    }

    public function getTaskforSubject($subject)
    {

        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $this->apiKey,
        ])->get("$this->apiUrl/issues.json", [
            'project_id' => $this->project,
            'subject' => $subject,
        ]);
        $subject = $response->json()['issues'][0]['subject'];
        $id = $response->json()['issues'][0]['id'];
        return $id;
    }

    public function getTaskForId($id)
    {
        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $this->apiKey,
        ])->get("$this->apiUrl/issues/{$id}.json");
        if ($response->failed()) {
            return null;
        }
        return $response->json();
    }

    public function checkLogtimeForThisMonth()
    {
        $developerNames = array_flip(config('information.developer_report'));
        $logtimesByDate = $this->getLogtimeForThisMonth();
        $totalLogTime = [];

        foreach ($logtimesByDate as $logtimes) {
            foreach ($logtimes as $entry) {
                $userName = $entry['user']['name'] ?? null;
                $hours = $entry['hours'] ?? 0;

                if ($userName && isset($developerNames[$userName])) {
                    $totalLogTime[$userName]['total_hours'] = ($totalLogTime[$userName]['total_hours'] ?? 0) + $hours;
                }
            }
        }

        return $totalLogTime;
    }

    public function getLogtimeForThisMonth(): array
    {
        $logtimeByDate = [];

        foreach ($this->getWorkingDaysOfThisMonth() as $date) {
            $response = Http::withHeaders([
                'X-Redmine-API-Key' => $this->apiKey,
            ])->get("{$this->apiUrl}/time_entries.json", [
                'spent_on'   => $date,
                'project_id' => $this->project,
            ]);

            $logtimeByDate[$date] = $response->json()['time_entries'] ?? [];
        }

        return $logtimeByDate;
    }

    public function getWorkingDaysOfThisMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $dates = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if (!$date->isWeekend()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }

    public function executeLogtimeForThisMonth(array $data)
    {
        $logtimeByDate = [];
        $developerKey = $data['developer'];
        unset($data['developer']);
        foreach ($data as $date => $tasks) {
           foreach ($tasks as $key => $task) {
            if ($task['task_id']) {
                $logtimeByDate[$date][$key] = [
                    'spent_time' => $task['spent_time'],
                    'task_id' => (int) $task['task_id'],
                    'date' => $date,
                    'key' => $developerKey,
                    'activity_id' => $task['activity_id'],
                    ];
                }
            }
        }
        $taskErrors = [];
        $taskSuccess = [];
        if(count($logtimeByDate) == 0) {
            return ['error' => 'Không có task được log time',
                    'taskErrors' => $taskErrors];
        }

        foreach ($logtimeByDate as $item) {
            foreach ($item as $logtime) {
                if( ! $this->getTaskForId($logtime['task_id'])) {
                    $taskErrors[$logtime['date']][] = $logtime['task_id'];
                    continue;
                }
                $taskSuccess[] = $logtime['task_id'];
                $this->logTimeToRedmine($logtime);
            }
        }
        if(count($taskSuccess) == 0) {
            return ['error' => 'Không có task được log time',
                    'taskErrors' => $taskErrors];
        }
        return [
                'taskSuccess' => $taskSuccess, 
                'taskErrors' => $taskErrors
            ];
    }

    /**
     * Xoá logtime theo ID
     *
     * @param int $timeEntryId
     * @param string $user
     * @return bool
     */
    public function deleteLogTime(int $timeEntryId, string $user)
    {
        $result = [];
        $userKey = config('information.user_for_key')[$user] ?? null;
        if (!$userKey) {
            $result['error'] = 'Không tìm thấy user key cho ' . $user;
            return $result;
        }
        $url = rtrim($this->apiUrl, '/') . "/time_entries/{$timeEntryId}.json";

        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $userKey,
            'Content-Type' => 'application/json',
        ])->delete($url);

        // Redmine trả về 204 No Content nếu xoá thành công
        $result = [
            'status' => $response->status(),
        ];
        return $result;
    }
   
}