<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/2/2018
 * Time: 8:30 PM
 */

class Post
{
    public $title;
    public $published;

    public function __construct($title, $published) {
        $this->title = $title;
        $this->published = $published;
    }
}

$posts = [
    new Post("Julius 1st Blog Post", true),
    new Post("Julius 2nd Blog Post", true),
    new Post("Julius 3rd Blog Post", true),
    new Post("Julius 4th Blog Post", false),
];

$unpublishedPosts = array_filter($posts, function($post) {
    return !$post->published;
});

$publishedPosts = array_filter($posts, function($post) {
    return $post->published;
});

// array_map() is good for transforms
$modified = array_map(function($post) {
    /* can cast to an array */
    // return (array)$post;

    /* just return a subset from class */
    return ['title' => $post->title];

    /* wrap title in another class */
    // return new SomeOtherClass($post->title);
}, $posts);

// pass in a "public property of class" or a "key of an array"
$titles = array_column($posts, 'title');

$posts = array_map(function($post) {
    return (array)$post;
}, $posts);

// now it's an assoc.ar
$titles = array_column($posts, 'title', 'title');

var_dump($titles);