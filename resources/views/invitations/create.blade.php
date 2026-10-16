<html>
    <head>
        <title>Invite User</title>
    </head>
<body>
    <h1>Invite User</h1>
    <form method="POST" action="{{ route('invitations.store') }}">
        @csrf
        <p>Name: <input type="text" name="name" required value="{{ old('name') }}"></p>
        <p>Email: <input type="email" name="email" required value="{{ old('email') }}"></p>

        @if(auth()->user()->isSuperAdmin())
            <p>Role:
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="member">Member</option>
                    <option value="sales">Sales</option>
                    <option value="manager">Manager</option>
                </select>
            </p>
            <p>Company ID: <input type="number" name="company_id" required value="{{ old('company_id') }}"></p>
        @else
            <p>Role:
                <select name="role" required>
                    <option value="sales">Sales</option>
                    <option value="manager">Manager</option>
                </select>
            </p>
        @endif

        <button type="submit">Send Invite</button>
    </form>
    @if($errors->any())
        <ul style="color:red">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    @endif
</body></html>