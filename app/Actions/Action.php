<?php

namespace App\Actions;

use App\Exceptions\HostActionException;
use App\Models\Host;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Action
{
    protected $http;

    public function __construct()
    {
        $this->http = Http::remote()->asForm();
    }

    public function createTask($host, $title, $status = 'processing', $progress = null)
    {
        if ($host instanceof Host) {
            $host_id = $host->host_id;
        } else {
            $host_id = $host;
        }

        $task = $this->http->post('/tasks', [
            'title' => $title,
            'host_id' => $host_id,
            'status' => $status,
            'progress' => $progress,
        ]);

        $task_json = $task->json();
        if ($task->failed()) {
            Log::error($task_json);
            return false;
        } else {
            return $task_json['data']['id'] ?? false;
        }
    }

    protected function updateTask($task_id, $title = null, $status = null, $progress = null)
    {
        $append = [];

        if ($title) {
            $append['title'] = $title;
        }

        if ($status) {
            $append['status'] = $status;
        }

        if ($progress) {
            $append['progress'] = $progress;
        }

        // 完成任务
        $resp = $this->http->patch('/tasks/' . $task_id, $append);

        if ($resp->failed()) {
            Log::error($resp->body());
            return false;
        } else {
            return true;
        }
    }

    public function createCloudHost(float $price, array $data = [])
    {
        // 过滤掉不需要的数据
        $data = Arr::except($data, ['id', 'user_id', 'host_id', 'price', 'managed_price', 'suspended_at', 'created_at', 'updated_at']);

        $data = array_merge([
            'price' => $price,
            'status' => 'pending',
            'user_id' => auth('user')->id(),
        ], $data);

        $resp = $this->http->post('/hosts', $data);

        $resp_json = $resp->json();

        if ($resp->failed()) {
            Log::error($resp_json);
            throw new HostActionException($resp_json['message'] ?? '创建云主机失败');
        } else {

            $host_id = $resp_json['data']['id'];
            $data['host_id'] = $host_id;

            $host = Host::create($data);
            return $host;
        }
    }

    protected function updateCloudHost($host, array $data = [])
    {
        if ($host instanceof Host) {
            $host_id = $host->host_id;
        } else {
            $host_id = $host;
        }

        $resp = $this->http->patch('/hosts/' . $host_id, $data);

        if ($resp->failed()) {
            Log::error($resp->body());
            return false;
        } else {
            return $resp->json();
        }
    }

    protected function deleteCloudHost($host)
    {
        if ($host instanceof Host) {
            $host_id = $host->host_id;
        } else {
            $host_id = $host;
        }

        $resp = $this->http->delete('/hosts/' . $host_id);

        if ($resp->failed()) {
            Log::error($resp->body());
            return false;
        } else {
            return true;
        }
    }
}
