<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\WorkOrder\WorkOrder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $users = new User();
        $count = User::count();

        foreach ($request->except(['page']) as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if ($request->{$key}) {
                $users = $users->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        $count = $users->count();

        $users = $users->simplePaginate(100);

        return view('users.index', ['users' => $users, 'count' => $count]);
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
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $hosts = Host::where('user_id', $user->id)->latest()->paginate(50, ['*'], 'hosts_page');
        $workOrders = WorkOrder::where('user_id', $user->id)->latest()->paginate(50, ['*'], 'workOrders_page');

        return view('users.show', compact('hosts', 'workOrders', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
