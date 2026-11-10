<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media.index', ['items' => Media::latest()->paginate(24)]);
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:8192', 'dimensions:min_width=600,min_height=300,max_width=8000,max_height=8000'],
            'alt' => ['required', 'string', 'min:5', 'max:200'],
            'credit' => ['nullable', 'string', 'max:200'],
        ], [
            'alt.required' => 'Alt text is required: describe the image for people who cannot see it (also good for SEO).',
            'file.max' => 'The image must be 8 MB or smaller.',
            'file.dimensions' => 'The image must be at least 600 x 300 pixels and no larger than 8000 x 8000.',
        ]);

        $images->store($request->file('file'), strip_tags($data['alt']), null, $data['credit'] ?? null);

        return back()->with('status', 'Image uploaded and optimised to WebP.');
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate(['alt' => ['required', 'string', 'min:5', 'max:200'], 'credit' => ['nullable', 'string', 'max:200']]);
        $media->update(['alt' => strip_tags($data['alt']), 'credit' => $data['credit'] ?? null]);

        return back()->with('status', 'Image details saved.');
    }

    public function destroy(Media $media)
    {
        $media->deleteFiles();
        $media->delete();

        return back()->with('status', 'Image deleted.');
    }
}
