<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $taskFormatted = $taskName['tracker'] . ' #' . $taskName['id'] . ' :'  . $taskName['subject'] . ' ' ;
            $groupedTasks[$user][] = [
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

    public function createDailyReport($data)
    {
        try {
            $today = date('Y-m-d');
            $subject = '日報　' . date('Y年n月j日');
            
            // Format the description
            $description = "*1.【定量報告】*\n\n";
            
            // Front section
            $description .= "* Front\n";
            $description .= "** CR/Overlooked: Done/Total: 34/34\n";
            $description .= "** Bug: Fixed/Total: 27/28\n";
            $description .= "** Pending: 1\n\n";
            
            // EC section
            $description .= "* EC\n";
            $description .= "** CR/Overlooked: Done/Total:99/100\n";
            $description .= "** Bug: Fixed/Total: 33/33\n\n";
            
            // API section
            $description .= "* API\n";
            $description .= "** CR/Overlooked: Done/Total:4/4\n";
            $description .= "** Bug: Fixed/Total: 0/0\n\n";
            
            // Today's tasks section
            $description .= "*2.【本日のタスク】*\n\n";
            $description .= $this->formatTasksTable($data) . "\n\n";
            
            // Notices section
            $description .= "*3.【連絡事項】*\n\n";
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
        $developers = [
            'VinhDV', 'QuyLV', 'KietNA', 'DuongNT', 'PhuDT', 'YenNH','ThienND','ChuongNPN', 'BaoNC', 'DuyTT', 'NganPVH'
        ];

        foreach ($developers as $dev) {
            $table .= "| {$index} |{$splus}{$dev}| ";
            
            // Add tasks
            if (isset($data[$dev])) {
                foreach ($data[$dev] as $task) {
                    $taskContent = is_array($task['task']) ? implode(' | ', $task['task']) : $task['task'];
                    $table .= $taskContent . "\n";
                }
            }
            
            $table .= "| ";
            
            // Add statuses
            if (isset($data[$dev])) {
                foreach ($data[$dev] as $task) {
                    $status = is_array($task['status']) ? implode(' | ', $task['status']) : $task['status'];
                    $taskStatus = $status == 'Closed' || $status == 'Resolved' ? '完了' : '進行中';
                    $table .= $taskStatus . "\n";
                }
            }
            
            $table .= "|. |\n";
            $index++;
        }

        return $table;
    }

    function logTimeToRedmine(array $data)
    {   
        $redmineUrl = $this->apiUrl; 
        $apiKey =  $data['key']; 

        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("$redmineUrl/time_entries.json", [
            'time_entry' => [
                'issue_id'    => (int) $data['task_id'],
                'hours'       => $data['spent_time'],
                'spent_on'    => Carbon::now()->format('Y-m-d'),
                'activity_id' => $data['activity_id'],
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to log time to Redmine: ' . $response->body());
        }

        return $response->json();
    }
}
