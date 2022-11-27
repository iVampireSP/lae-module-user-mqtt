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

        $device_allows = DeviceAllow::where('device_id', $device->id)
            ->where('type', $type)
            ->get();

        // Log::debug("message", [
        //     'device_allows' => $device_allows,
        //     'topic' => $topic,
        // ]);

        foreach ($device_allows as $device_allow) {

            // 先精确匹配
            if ($device_allow->topic == $topic) {
                // Log::info('精确匹配', [
                //     'topic' => $topic,
                //     'device_allow' => $device_allow->toArray(),
                // ]);
                if ($device_allow->action == 'deny') {
                    return $this->forbidden('禁止订阅', 403);
                }
            }

            // 将 topic 转换成适合模糊搜索的格式
            $topic = str_replace('#', '%', $topic);
            $topic = str_replace('+', '_', $topic);

            // 将 #,%,+ 转换成 *
            $allow_topic = str_replace('%', '*', $device_allow->topic);
            $allow_topic = str_replace('_', '*', $allow_topic);
            $allow_topic = str_replace('#', '*', $allow_topic);

            // Log::debug('$device_allow->topic', [$allow_topic]);

            if (fnmatch($allow_topic, $topic)) {
                return $this->success([
                    'result' => true,
                ]);
            }
        }


        return $this->forbidden('禁止访问', 403);
    }
}
