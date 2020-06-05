<?php


namespace Kaban\Components\Admin\Posts\Controllers;


use Illuminate\Http\Request;
use Kaban\Components\Admin\Posts\Resources\GetAllPostsResource;
use Kaban\General\Enums\EPostStatus;
use Kaban\Models\Post;

class PostsController {
    public function index( Request $request ) {
        return view( 'AdminPosts::index' );
    }

    public function edit( $id ) {
        $post = Post::with( 'tags' )->findOrFail( $id );
        //next create categoty component then back to here
        dd( $post );

        return view( 'AdminPosts::index' );
    }

    public function all() {
        $posts = Post::paginate( 2 );

        return GetAllPostsResource::collection( $posts );
//        return new GetAllPostsResource( $posts );
    }

    public function store( Request $request ) {
        $uid    = auth()->id();
        $item   = Post::create( [
            'content'    => $request->content,
            'title'      => $request->title,
            'excerpt'    => $request->excerpt,
            'author_id'  => $uid,
            'created_by' => $uid,
            'updated_by' => $uid,
            'status'     => EPostStatus::approved,
            'slug'       => slugify( 'title' )
        ] );
        $tagIds = $item->syncTags( $request->input( 'tag', [] ) );

        $item->tag_ids = $tagIds;
        $item->save();
    }
}
