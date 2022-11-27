<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\Server;

// use Illuminate\Http\Request;

class RemoteController extends Controller
{
    // invoke
    public function __invoke()
    {
        $data = [
            'remote' => [
                'name' => config('remote.module_id'),
            ],
            'servers' => Server::all()->toArray()
        ];

        return $this->success($data);
    }
}
