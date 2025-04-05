<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::paginate(10)
            ->through(function ($page) {
                return [
                    'id' => $page->id,
                    'name' => $page->name,
                    'slug' => $page->slug,
                ];
            });

        return Inertia::render('Users/Admin/Page/Index', ['pages' => $pages]);
    }
    public function edit($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return Inertia::render('Users/Admin/Page/Edit', ['page' => $page]);
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page = Page::where('slug', $slug)->firstOrFail();
        $page->update($request->all());

        return back()->with('message_success', 'Page updated successfully');
    }
}
