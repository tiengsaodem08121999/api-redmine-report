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
        $this->apiKey = "261a83492179548e45039abffc8f67434922744b"; // Đặt API Key trong file .env
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
}
