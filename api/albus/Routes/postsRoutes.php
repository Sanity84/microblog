<?php 

// Validation for post/put
$post_validation = new albus\Core\Validation();
$post_validation->setRules(array(
	'author' => array(
		'required',
		'minLength' => 2,
		'maxLength' => 20
	),
	'content' => array(
		'required',
		'minLength' => 1,
		'maxLength' => 255
	)
));

$router->get('/', function() use ($res) {
	$res->setContentType('text/html');
	$template = '
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<title>AlbusPHP Framework - Microblog</title>
		<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<h1>AlbusPHP Framework (Microblog)</h1>
					<p class="lead">Routes</p>
					<ul class="list-group">
						<li class="list-group-item">
							<h4>GET /api/posts(/:postid)?args</h4><br>
							Retrieve list of specified posts or single post if postid is provided. Args available are:
							author,limit,offset and fields = id,author,content,created<br>
							<small>Example: <a href="http://andrewtorrez.com/microblog/api/posts/?limit=3&fields=author,content,created">http://andrewtorrez.com/microblog/api/posts/?limit=3&fields=author,content,created</a></small>
						</li>
						<li class="list-group-item">
							<h4>POST /api/posts</h4><br>
							Post json to server
						</li>
						<li class="list-group-item">
							<h4>PUT /api/posts/:postid</h4><br>
							Create/Update post on server with provided id
						</li>
						<li class="list-group-item">
							<h4>DELETE /api/posts/:postid</h4><br>
							Delete post from server
						</li>
					</ul>
				</div>
			</div>
		</div>
	</body>
	</html>
	';
	echo $template;
});

$router->get('/posts(/:postid)', function($postid) use ($res, $db) {
	// all return types are json
	$res->setContentType('application/json');

	try {
		$sql = '';
		$posts = array();

		$args = array(
			':author' => isset($_GET['author']) ? $_GET['author'] : '%',
			':limit' => isset($_GET['limit']) ? $_GET['limit'] : 25,
			':offset' => isset($_GET['offset']) ? $_GET['offset'] : 0,
		);
		// not specific post was requested
		if($postid == null)
			$sql = 'SELECT * FROM posts WHERE author LIKE :author LIMIT :limit OFFSET :offset';
		else {
			$sql = 'SELECT * FROM posts WHERE id=:postid AND author LIKE :author LIMIT :limit OFFSET :offset';
			$args[':postid'] =  $postid;
		}

		$stmt = $db->getDb()->prepare($sql);
		$stmt->execute($args);

		$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(!$posts) {
			echo json_encode($res->notfound(array('message' => 'Post not found')), JSON_PRETTY_PRINT);
			return;
		}

		// filter by user defined fields
		if(isset($_GET['fields'])) {
			$fields = array_flip(explode(',', $_GET['fields']));

			foreach($posts as $key => $u)
				$posts[$key] = array_intersect_key($u, $fields);
		}

		// Fix to return a single record instead of an array after checking fields
		if($postid)
			$posts = $posts[0];
		
	}catch(PDOException $e) {
		echo json_encode($res->error(array('message' => $e->getMessage())), JSON_PRETTY_PRINT);
		return;
	}

	echo json_encode($res->ok($posts), JSON_PRETTY_PRINT);

});

$router->post('/posts', function() use ($req, $res, $db, $post_validation) {
	$res->setContentType('application/json');
	$body = json_decode($req->getBody(), true);

	if(!$post_validation->test($body)) {
		echo json_encode($res->error(array('message' => $post_validation->getMessage())), JSON_PRETTY_PRINT);
		return;
	}
	$post = array();
	try {
		$id;
		$db->getDb()->beginTransaction();
		$stmt = $db->getDb()->prepare('INSERT INTO posts (author, content) VALUES (:author, :content)');
		$insert = $stmt->execute($db->prepareData($body, $post_validation->getRules()));
		if(!$insert) {
			$db->getDb()->rollBack();
			echo json_encode($res->error(array('message' => 'An error occured')), JSON_PRETTY_PRINT);
			return;
		}
		$id = $db->getDb()->lastInsertId();
		$db->getDb()->commit();

		// retrieve newly inserted record
		$stmt = $db->getDb()->prepare('SELECT * FROM posts WHERE id=:postid LIMIT 1');
		$stmt->execute(array(':postid' => $id));
		$post = $stmt->fetch(PDO::FETCH_ASSOC);

	}catch(PDOException $e) {
		echo json_encode($res->error(array('message' => $e->getMessage())), JSON_PRETTY_PRINT);
		return;
	}

	echo json_encode($res->created($post), JSON_PRETTY_PRINT);
});