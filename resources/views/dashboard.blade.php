<html>
    <head>
        <title>Dashboard</title>
    </head>
<body>
    <h1>Dashboard</h1>
    <p>Welcome {{ auth()->user()->name }} — Role: {{ auth()->user()->role }}</p>

    <ul>
        <li><a href="{{ route('short-urls.index') }}">View Short URLs</a></li>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
            <li><a href="{{ route('invitations.create') }}">Invite User</a></li>
        @endif
    </ul>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button>Logout</button>
    </form>
</body>
</html>