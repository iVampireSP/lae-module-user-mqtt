<?php

namespace App\Http\Controllers;

use App\Models\Host;
use Illuminate\Http\Request;
use App\Models\WorkOrder\WorkOrder;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $hosts = Host::with('user');

        foreach ($request->except(['page']) as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if ($request->{$key}) {
                $hosts = $hosts->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        $count = $hosts->count();

        $hosts = $hosts->simplePaginate(100);

        return view('hosts.index', ['hosts' => $hosts, 'count' => $count]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function show(Host $host)
    {
        //
        $workOrders = WorkOrder::where('host_id', $host->id)->orderBy('id', 'desc')->paginate(100);

        return view('hosts.show', ['host' => $host, 'workOrders' => $workOrders]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function edit(Host $host)
    {
        //
        return view('hosts.edit', ['host' => $host]);
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
        //
        $request->validate([
            'status' => 'nullable|in:stopped,running,suspended,error,cost,pending',
            'managed_price' => 'nullable|numeric',
        ]);

        // if status is cost
        if ($request->status == 'cost') {
            $this->http->patch('hosts/' . $host->host_id, [
                'cost_once' => $host->price,
            ]);
            return back()->with('success', '已发送扣费请求。');
        }

        $host->update($request->only(['status', 'managed_price']));


        return back()->with('success', '正在执行对应的操作，操作将不会立即生效，因为他需要进行同步。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        // 销毁前的逻辑

        $HostController = new Remote\Functions\HostController();
        $HostController->destroy($host);

        return back()->with('success', '已开始销毁。');
    }
}
