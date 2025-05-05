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
        $this->apiUrl = "https://tools.splus-software.com/redmine";
        $this->apiKey = "261a83492179548e45039abffc8f67434922744b";
        $this->project = 's7-ec-cube';
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
    public function checkPCV()
    {
        $today = Carbon::today()->toDateString();
        $statuses = ['New', 'In Progress', 'Feedback'];
    
        $response = Http::get("{$this->apiUrl}/issues.json", [
            'project_id' => $this->project,
            'status_id' => '*',
            'limit' => 100,
            'key' => $this->apiKey,
        ]);
    
        if (!$response->successful()) {
            return [];
        }
    
        $issues = $response->json('issues') ?? [];
        $lateIssues = [];
    
        foreach ($issues as $issue) {
            $startDate = $issue['start_date'] ?? null;
            $dueDate = $issue['due_date'] ?? null;
            $status = $issue['status']['name'] ?? null;
            $tracker = $issue['tracker']['name'] ?? null;
    
            if (
                $startDate && $startDate <= $today &&
                $dueDate && $dueDate <= $today &&
                in_array($status, $statuses) &&
                $tracker !== 'Report'
            ) {
                $lateIssues[] = $issue['id'];
            }
        }
    
        return $lateIssues;
    }
    
    public function updateIssues(array $ids)
    {
        $today = Carbon::today();
        $futureDate = $today->copy()->addDays(2);

        // Nếu rơi vào thứ 7 hoặc Chủ nhật thì chỉnh thành thứ 2 tuần sau
        if ($futureDate->isWeekend()) {
            $futureDate->next(Carbon::MONDAY);
        }

        $dueDate = $futureDate->toDateString();

        foreach ($ids as $id) {
            // Lấy thông tin hiện tại của issue để kiểm tra status
            $response = Http::get("{$this->apiUrl}/issues/{$id}.json", [
                'key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                // Ghi log hoặc tiếp tục tùy ý
                continue;
            }

            $issue = $response->json('issue');
            $currentStatus = $issue['status']['name'] ?? null;

            // Nếu đang là "New", thì chuyển sang "In Progress" (giả sử ID = 2 là In Progress)
            $statusId = null;
            if ($currentStatus === 'New') {
                $statusId = 2; // Bạn cần xác định ID thật của status "In Progress" trong hệ thống Redmine
            }

            $payload = [
                'issue' => [
                    'due_date' => $dueDate,
                ],
            ];

            if ($statusId) {
                $payload['issue']['status_id'] = $statusId;
            }

            // Gửi yêu cầu cập nhật
            Http::put("{$this->apiUrl}/issues/{$id}.json", $payload + ['key' => $this->apiKey]);
        }

        return response()->json([
            'message' => 'Update due date successfully!',
            'due_date_set' => $dueDate,
        ]);
    }
}
