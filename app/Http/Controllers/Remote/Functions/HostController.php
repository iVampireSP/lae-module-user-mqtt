<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Host;
use App\Actions\HostAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Exceptions\HostActionException;
use App\Models\HostAllow;

class HostController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);
        $hosts = Host::thisUser()->get();
        return $this->success($hosts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|max:32',
        ]);

        $hostAction = new HostAction();

        if (Host::where('user_id', auth('user')->id())->count() >= 1) {
            return $this->error('您已经添加过主机了。');
        }

        $host = $hostAction->create($request->all());

        return $this->created($host);
    }

    public function show(Host $host)
    {
        $this->isUser($host);

        return $this->success($host);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Host $host)
    {
        $this->isUser($host);

        $request->validate([
            'password' => 'required|string|min:8|max:32',
        ]);

        // 排除 request 中的一些参数
        $request_only = $request->except(['id', 'user_id', 'host_id', 'price', 'managed_price', 'suspended_at', 'created_at', 'updated_at']);

        $hostAction = new HostAction();

        $host = $hostAction->update($host, $request_only);

        return $this->updated($host);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        $this->isUser($host);

        // 具体删除逻辑
        $hostAction = new HostAction();

        try {
            $hostAction->destroy($host);
        } catch (HostActionException $e) {
            $this->error($e->getMessage());
        }

        return $this->deleted($host);
    }


    public function isUser(Host $host)
    {
        // return $host->user_id == Auth::id();

        if (request('user_id') !== null) {
            if ($host->user_id != request('user_id')) {
                abort(403);
            }
        }
    }

    public function allows(Host $host)
    {
        $this->isUser($host);

        $allows = HostAllow::where('host_id', $host->host_id)->get();

        return $this->updated($allows);
    }


    public function addAllow(Request $request, Host $host)
    {
        $this->isUser($host);

        $request->validate([
            'host_id' => 'required|exists:hosts,host_id',
        ]);

        if ($request->host_id === $host->host_id) {
            return $this->error('您不能添加自己的主机。');
        }

        // 检测是否重复
        if (HostAllow::where('host_id', $host->host_id)->where('allow_host_id', $request->host_id)->count() > 0) {
            return $this->error('您已经添加过该主机了。');
        }

        HostAllow::create([
            'allow_host_id' => $request->host_id,
            'host_id' => $host->host_id,
        ]);

        return $this->created();
    }

    public function deleteAllow(Request $request, Host $host, Host $allow)
    {
        $this->isUser($host);

        HostAllow::where('host_id', $host->host_id)->where('allow_host_id', $allow->host_id)->delete();

        return $this->deleted();
    }
}
