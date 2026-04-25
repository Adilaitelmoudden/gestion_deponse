<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $tags   = Tag::where('user_id', $userId)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $userId = session('user_id');

        $exists = Tag::where('user_id', $userId)->where('name', $validated['name'])->exists();

        if ($exists) {
            return back()->with('error', 'Un tag avec ce nom existe déjà.')->withInput();
        }

        $validated['user_id'] = $userId;
        $validated['color']   = $validated['color'] ?? '#6366f1';

        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag créé avec succès!');
    }

    public function update(Request $request, Tag $tag)
    {
        if ($tag->user_id != session('user_id')) abort(403);

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag mis à jour!');
    }

    public function destroy(Tag $tag)
    {
        if ($tag->user_id != session('user_id')) abort(403);
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag supprimé.');
    }

    public function apiList()
    {
        $tags = Tag::where('user_id', session('user_id'))->orderBy('name')->get(['id', 'name', 'color']);
        return response()->json($tags);
    }
}
