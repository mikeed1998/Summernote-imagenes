<?php

namespace App\Http\Controllers;

use App\Post;
use DOMDocument;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('index', compact('posts'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $description = $request->description;

        $dom = new DOMDocument();
        $dom->loadHTML($description, 9);

        $images = $dom->getElementsByTagName('img');

        foreach($images as $key => $img) {
            $data = base64_decode(explode(',', explode(';', $img->getAttribute('src'))[1])[1]);
            $image_name = "/upload/" . time(). $key. '.png';

            file_put_contents(public_path().$image_name, $data);

            $img->removeAttribute('src');
            $img->setAttribute('src', $image_name);
        }

        $description = $dom->saveHTML();

        Post::create([
            'title' => $request->title,
            'description' => $description,
        ]);

        return redirect('/');
    }

    public function edit(Post $post) {
        return view('edit', compact('post'));
    }

    public function update(Request $request, Post $post) 
    {
        $description = $request->description;

        $dom = new DOMDocument();
        $dom->loadHTML($description, 9);

        $images = $dom->getElementsByTagName('img');

        foreach($images as $key => $img) {

            if(strpos($img->getAttribute('src'), 'data:image/') === 0) {
                $data = base64_decode(explode(',', explode(';', $img->getAttribute('src'))[1])[1]);
                $image_name = "/upload/" . time(). $key. '.png';

                file_put_contents(public_path().$image_name, $data);

                $img->removeAttribute('src');
                $img->setAttribute('src', $image_name);
            }
        }

        $description = $dom->saveHTML();

        $post->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return redirect('/');
    }

    public function show(Post $post) {
        return view('show', compact('post'));
    }

    public function destroy(Post $post) {
        $dom = new DOMDocument();
        $dom->loadHTML($post->description, 9);
        $images = $dom->getElementsByTagName('img');

        foreach($images as $key => $img) {
            
            $src = $img->getAttribute('src');
            $imagePath = public_path($src);

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $post->delete();
        return redirect()->back();
    }
}
