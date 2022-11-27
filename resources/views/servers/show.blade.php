<x-app-layout>
    <h2>服务器 {{ $server->name }}</h2>

    <a href="{{ route('servers.edit', $server) }}">编辑服务器</a>


    <input type="text" name="name" placeholder="服务器名称" readonly value="{{ $server->name }} " />


    <input type="text" name="fqdn" placeholder="服务器域名" readonly value="{{ $server->fqdn }} " />


    <input type="text" name="port" placeholder="服务器端口" readonly value="{{ $server->port }} " />


    <p>{{ $server->status }}</p>

</x-app-layout>
