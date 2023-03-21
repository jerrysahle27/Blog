<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->blogs;

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    public function show($id)
    {
        $post = auth()
            ->user()
            ->blogs()
            ->find($id);

        if (!$post) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Post not found ',
                ],
                400
            );
        }

        return response()->json(
            [
                'success' => true,
                'data' => $post->toArray(),
            ],
            400
        );
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->user_id = auth()->user()->id;

        if (
            auth()
                ->user()
                ->blogs()
                ->save($blog)
        ) {
            return response()->json([
                'success' => true,
                'data' => $blog->toArray(),
            ]);
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'blog not added',
                ],
                500
            );
        }
    }

    public function update(Request $request, $id)
    {
        $post = auth()
            ->user()
            ->posts()
            ->find($id);

        if (!$post) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Post not found',
                ],
                400
            );
        }

        $updated = $post->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Post can not be updated',
                ],
                500
            );
        }
    }

    public function destroy($id)
    {
        $post = auth()
            ->user()
            ->posts()
            ->find($id);

        if (!$post) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Post not found',
                ],
                400
            );
        }

        if ($post->delete()) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Post can not be deleted',
                ],
                500
            );
        }
    }
}
