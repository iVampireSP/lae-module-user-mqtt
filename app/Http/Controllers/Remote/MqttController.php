<?php

namespace App\Http\Controllers\Remote;

use App\Models\Host;
use App\Models\Device;
use App\Models\HostAllow;
use App\Models\DeviceAllow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MqttController extends Controller
{
    //

    public function authentication(Request $request)
    {
        $host_id = $request->input('device_id');
        $password = $request->input('password');

        $host = Host::where('host_id', $host_id)->firstOrFail();

        if ($host->password !== $password) {
            return $this->error('密码错误');
        }

        return $this->success($host);
    }

    public function authorization(Request $request)
    {
        $host_id = $request->input('device_id');

        $topic = $request->input('topic');

        $topics = explode('/', $topic);

        if (count($topics) === 1) {
            return $this->error('主题错误');
        }

        $next_host_id = $topics[1];

        $host = Host::where('host_id', $host_id)->firstOrFail();

        if ($next_host_id == $host->host_id) {
            return $this->success($host);
        } else {
            // 不属于同一主机，检测是否在 HostAllow 中

            $allow = HostAllow::where('host_id', $next_host_id)->where('allow_host_id', $host->host_id)->exists();

            if (!$allow) {
                return $this->error('主机不允许访问');
            } else {
                return $this->success();
            }
        }
    }
}
