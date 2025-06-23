<?php

namespace App\Http\Controllers\Dashboard;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all()->each(function ($tag) {
            return $tag->mergeCasts(['name' => Translated::class]);
        });

        return dataJson('tags', $tags, 'All Tags.');
    }

    public function show($id)
    {
        $tag = Tag::find($id);
        if (!$tag)
            return messageJson('Tag not found.!', false, 404);

        return dataJson('tag', $tag, "Tag with Id: $id returned.");
    }

    public function store(TagRequest $request)
    {
        Tag::create($request->all());

        return messageJson('New tag created.', true, 201);
    }

    public function update(TagRequest $request, $id)
    {
        $tag = Tag::find($id);
        if (!$tag)
            return messageJson('Tag not found.!', false, 404);

        $tag->update($request->all());

        return messageJson('The tag updated successfully.');
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag)
            return messageJson('Tag not found.!', false, 404);

        $tag->delete();

        return messageJson('The tag deleted successfully.');
    }
}
