<?php 
include '../vendor/autoload.php';
include 'includes.php';

$app = new \Slim\Slim();

// Set header for json data responses
$res = $app->response();
$res->header('Content-Type', 'application/json');

// Capture post protocol on URI /post
$app->post('/post', function() use ($app) {
	// Create new Post object
	$post = new Post();

	// Decode body of json posted data into PHP associative array
	$post_data = json_decode($app->request->getBody(), true);

	echo json_encode($post->createPost($post_data), JSON_PRETTY_PRINT);
});

// Capture get protocol on URI /post with optional id parameter 
// NOTE: the parentheses means optional, these can only be on ends of URIs
$app->get('/post(/:postid)', function($postid = null) use ($app) {
	$post = new Post();

	// If no postid passed, retrieve a list of all posts
	if($postid === null)
		echo json_encode($post->getPosts(), JSON_PRETTY_PRINT);
	else // Retreive single post
		echo json_encode($post->getPost($postid), JSON_PRETTY_PRINT);
});

// Capture put protocol on URI /post with mandatory id parameter
$app->put('/post/:postid', function($postid = null) use ($app) {
	$post = new Post();

	$post_data = json_decode($app->request->getBody(), true);
	echo json_encode($post->updatePost($post_data, $postid), JSON_PRETTY_PRINT);
});

$app->delete('/post/:postid', function($postid = null) use ($app) {
	$post = new Post();
	echo json_encode($post->deletePost($postid), JSON_PRETTY_PRINT);
});

$app->run();













