<html><head><title>Create Short URL</title></head>
<body>
    <h1>Create Short URL</h1>
    <form method="POST" action="{{ route('short-urls.store') }}">
        @csrf
        <label>Original URL:
            <input type="url" name="original_url" required value="{{ old('original_url') }}">
        </label>
        <button type="submit">Create</button>
        @error('original_url')<p style="color:red">{{ $message }}</p>@enderror
    </form>
    <a href="{{ route('short-urls.index') }}">Back</a>
</body></html>