<?php 
include 'fetch.inc.php';

// *****************
// Create new post
// *****************
$response = fetch('http://localhost/demos/rest/api/post', 'POST', 
	array('author' => 'Awesome Man', 'content' => 'Pizza is awesome!'));

if($response['status'] == 200) {

	echo '<h2>Successfully created post</h2>';
}else {

	echo '<p>An error occured</p>';
	echo $response['data']['message'];
}

print_data($response);
$postid = $response['data']['id'];

// **************
// Update Post
// **************
$response = fetch("http://localhost/demos/rest/api/post/$postid", 'PUT', 
	array('content' => 'Pizza AND tacos are awesome!'));

if($response['status'] == 200) {

	echo '<h2>Successfully updated post</h2>';
}else {

	echo '<p>An error occured</p>';
	echo $response['data']['message'];
}

print_data($response);

// ****************
// Get Post
// ****************
$response = fetch("http://localhost/demos/rest/api/post/$postid", 'GET');
if($response['status'] == 200) {
	echo '<h2>Successfully retreived post</h2>';
}else {

	echo '<p>An error occured</p>';
	echo $response['data']['message'];
}
print_data($response);

// ***************
// Delete Post
// ***************
$response = fetch("http://localhost/demos/rest/api/post/$postid", 'DELETE');

if($response['status'] == 200) {

	echo '<h2>Successfully deleted post</h2>';
}else {

	echo '<p>An error occured</p>';
	echo $response['data']['message'];
}

echo '<h1>All tests completed successfully</h1>';














