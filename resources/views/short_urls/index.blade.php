<html>
<head><title>Short URLs</title></head>
<body>
    <h1>Short URLs</h1>
    <p>Logged in as: {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>

    @if(auth()->user()->canCreateShortUrl())
        <a href="{{ route('short-urls.create') }}">+ Create Short URL</a>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Code</th><th>Original URL</th><th>Company</th><th>Created By</th>
            </tr>
        </thead>
        <tbody>
        @forelse($urls as $url)
            <tr>
                <td>{{ $url->short_code }}</td>
                <td>{{ $url->original_url }}</td>
                <td>{{ $url->company->name ?? '-' }}</td>
                <td>{{ $url->user->name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No URLs visible to you.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>