<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAllow;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $devices = Device::paginate(100);

        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('devices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'name' => 'required|unique:devices',
            'password' => 'required|min:8|max:32',
            'client_id' => 'nullable',
        ]);

        $device = Device::create($request->all());

        return redirect()->route('devices.edit', $device)->with('success', '设备创建成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        //

        return redirect()->route('devices.edit', $device);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        //
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        $request->validate([
            'password' => 'required|min:8|max:32',
            'client_id' => 'nullable',
        ]);


        // 检测 name 重复
        if ($request->name != $device->name) {
            $request->validate([
                'name' => 'required|unique:devices',
            ]);
        }

        $device->update($request->all());

        // if password dirty
        if ($device->isDirty('password')) {
            // 断开连接
        }

        return redirect()->route('devices.edit', $device)->with('success', '设备更新成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        //

        $device->delete();

        return redirect()->route('devices.index')->with('success', '设备删除成功。');
    }


    public function allows(Device $device)
    {
        $allows = DeviceAllow::where('device_id', $device->id)->get();

        return view('devices.allows.index', compact('device', 'allows'));
    }

    public function store_allow(Request $request, Device $device)
    {
        $request->validate([
            'topic' => 'required',
            'action' => 'required|in:allow,deny',
            'type' => 'required|in:subscribe,publish'
        ]);





        // 检测冲突
        $conflict = DeviceAllow::where('device_id', $device->id)
            ->where('topic', $request->topic)
            ->where('type', $request->type)
            ->first();

        // 如果已经有同意的同 type 同 topic 的规则，就不再添加
        if ($conflict && $conflict->action == 'allow') {
            return back()->with('error', '已经有同意的同 type 同 topic 的规则，不再添加')->withInput();
        }

        // if ($conflict) {
        //     if ($conflict->action !== $request->action) {
        //         return back()->with('error', '已存在相同的规则。')->withInput();
        //     }

        //     if ($conflict->topic === $request->topic) {
        //         return back()->with('error', '已存在相同的规则。')->withInput();
        //     }
        // }

        DeviceAllow::create([
            'device_id' => $device->id,
            'topic' => $request->topic,
            'action' => $request->action,
            'type' => $request->type
        ]);

        return redirect()->route('devices.allows.index', $device)->with('success', '设备权限创建成功。');
    }

    public function allow_destroy($allow)
    {
        //
        // $allow->delete();
        DeviceAllow::find($allow)->delete();

        return back()->with('success', '设备权限删除成功。');
    }
}
