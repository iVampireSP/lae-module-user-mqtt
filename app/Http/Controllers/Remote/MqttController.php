<?php

namespace App\Http\Controllers\Remote;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeviceAllow;
use Illuminate\Support\Facades\Log;

class MqttController extends Controller
{
    //

    public function authentication(Request $request)
    {
        $client_id = $request->input('client_id');
        $device_id = $request->input('device_id');
        $password = $request->input('password');

        $device = Device::where('name', $device_id)->first();

        if (!$device) {
            return $this->notFound('No device found');
        }

        if ($device->client_id) {
            if ($device->client_id != $client_id) {
                return $this->failed('客户端 ID 不匹配', 403);
            }
        }

        if ($device->password == $password) {
            return $this->success([
                'result' => true,
            ]);
        }

        return $this->failed('用户名或密码错误', 401);
    }

    public function authorization(Request $request)
    {
        $device_id = $request->input('device_id');

        $topic = $request->input('topic');

        $type = $request->input('type');

        $device = Device::where('name', $device_id)->first();

        if (!$device) {
            return $this->failed('设备不存在', 404);
        }

        $device_allow = DeviceAllow::where('device_id', $device->id)
            ->where('topic', $topic)
            ->where('type', $type)
            ->firstOrFail();

        if ($device_allow->action == 'allow') {
            return $this->success();
        } else {
            return $this->forbidden('禁止访问', 403);
        }
    }
}
