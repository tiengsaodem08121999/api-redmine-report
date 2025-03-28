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

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = "https://tools.splus-software.com/redmine";
        $this->apiKey = env('REDMINE_API_KEY'); // Đặt API Key trong file .env
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
            ];
        }
        return $groupedTasks;
    }

    function getDailyReports($date, $projectId)
    {
        // URL API của Redmine
        $redmineUrl = "{$this->apiUrl}/issues.json";
        $apiKey = $this->apiKey;

        // Gọi API Redmine
        $response = Http::withHeaders([
            'X-Redmine-API-Key' => $apiKey
        ])->get($redmineUrl, [
            'project_id' => $projectId,
            'status_id' => '*',
            'tracker_id' => 8,
            'limit' => 100,
        ]);
    
        if ($response->successful()) {
            foreach ($response->json()['issues'] as $issue) {
                if($issue['tracker']['name'] === 'Report' && str_contains($issue['subject'], '日報')) {
                    $start_date = Carbon::parse($issue['start_date']);
                    $date = Carbon::parse($date);
                    if($start_date->month === $date->month) {
                        $data[] = $issue;
                    }
                }
            }
            $member = [];
            foreach($data as $issue) {
                $member[$issue['start_date']] = $this->TaskDailyReport($issue);
            }
            dd($member);
            return $data;
        }

        return response()->json(['error' => 'Failed to fetch reports'], 500);
    }
    function TaskDailyReport($issue) {
        $description = $issue['description'];
        $data = [];
        
        // Split the description into lines
        $lines = explode("\n", $description);
        
        // Find the table section
        $tableStart = false;
        foreach ($lines as $line) {
            if (strpos($line, '|_. # |_. 開発者 |_. ID タスク') !== false) {
                $tableStart = true;
                continue;
            }
            
            if ($tableStart) {
                // Skip empty lines and table header
                if (empty(trim($line)) || strpos($line, '|_.') !== false) {
                    continue;
                }
                
                // Parse table row
                $columns = array_map('trim', explode('|', $line));
                if (count($columns) >= 5) { // Ensure we have enough columns
                    $developer = $columns[2]; // Get developer name
                    // Extract task ID using regex
                    if (preg_match('/#(\d+)/', $columns[3], $matches)) {
                        if (!isset($data[$developer])) {
                            $data[$developer] = [];
                        }
                        $data[$developer][] = $matches[1];
                    }
                }
            }
        }
        return $data;
    }

    public function getProject() {
        $response = $this->client->request('GET', "{$this->apiUrl}/projects.json", [
            'query' => ['key' => $this->apiKey]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
