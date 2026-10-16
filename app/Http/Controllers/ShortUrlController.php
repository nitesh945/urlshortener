<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;

class ShortUrlController extends Controller
{
     public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $urls = collect();
        } elseif ($user->isAdmin()) {
            $urls = ShortUrl::where('company_id', '!=', $user->company_id)
                ->with(['company', 'user'])
                ->latest()
                ->get();
        } elseif ($user->isMember()) {
            $urls = ShortUrl::where('user_id', '!=', $user->id)
                ->with(['company', 'user'])
                ->latest()
                ->get();
        } else {
            $urls = ShortUrl::where('company_id', $user->company_id)
                ->with(['company', 'user'])
                ->latest()
                ->get();
        }

        return view('short_urls.index', compact('urls'));
    }

    public function create()
    {
        abort_unless(auth()->user()->canCreateShortUrl(), 403);
        return view('short_urls.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canCreateShortUrl(), 403);

        $request->validate([
            'original_url' => 'required|url|max:2048',
        ]);

        ShortUrl::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'original_url' => $request->original_url,
            'short_code' => ShortUrl::generateCode(),
        ]);

        return redirect()->route('short-urls.index')->with('success', 'Short URL created.');
    }

    public function redirect(string $code)
    {
        $url = ShortUrl::where('short_code', $code)->firstOrFail();
        return redirect($url->original_url);
    }
}
