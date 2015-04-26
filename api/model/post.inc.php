<?php 
class Post extends Model {

	public function createPost($post) {
		try {

			// Begain a transaction event to catch last inserted id
			$this->db->beginTransaction();
			// Prepare query for execution
			$stmt = $this->db->prepare("INSERT INTO posts (author, content) 
				VALUES (:author, :content)");
			// Execute query with passed parameters if they exist
			$stmt->execute(array(
				':author' => isset($post['author']) ? $post['author'] : null,
				':content' => isset($post['content']) ? $post['content'] : null
			));
			// Store id of inserted query
			$new_post_id = $this->db->lastInsertId();
			$this->db->commit();

			// Fetch complete record of newly inserted record
			$stmt = $this->db->prepare("SELECT * FROM posts WHERE id=:postid LIMIT 1");
			$stmt->execute(array(':postid' => $new_post_id));
			// Store result as an associative array
			$insert = $stmt->fetch(PDO::FETCH_ASSOC);

			// If no result (false) was returned, return error that nothing was saved
			if(!$insert) {
				$this->ERROR['data']['message'] = 'Could not save post';
				return $this->ERROR;
			}

			// Set response data to success, 200, and data to newly created record
			$this->OK['data'] = $insert;
			return $this->OK;

		// Used to catch MySQL level errors from contraints or misconstructed queries
		}catch(PDOException $e) {

			$this->ERROR['data']['message'] = $e->getMessage();
			return $this->ERROR;
		}
	}

	public function getPost($postid) {
		try {

			$stmt = $this->db->prepare("SELECT * FROM posts WHERE id=:postid LIMIT 1");
			$stmt->execute(array(':postid' => $postid));
			$get_post = $stmt->fetch(PDO::FETCH_ASSOC);

			if(!$get_post) {
				$this->ERROR['data']['message'] = 'Post does not exist';
				return $this->ERROR;
			}

			$this->OK['data'] = $get_post;
			return $this->OK;

		}catch(PDOException $e) {

			$this->ERROR['data']['message'] = $e->getMessage();
			return $this->ERROR;
		}
	}

	public function getPosts() {

		try {

			$stmt = $this->db->prepare("SELECT * FROM posts ORDER BY created DESC");
			$stmt->execute();
			// Note fetchAll, instead of fetch. fetchAll fetches mutiple records
			$get_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Two methods for retrieving all rows, wrap in success message, or just return data
			// a success message is reduntant because this method cannot return otherwise (no parameters are passed)
			
			// return data wrapped in success message
			// $this->OK['data'] = $get_posts;
			// return $this->OK;

			// Return raw data as array
			return $get_posts;

		}catch(PDOException $e) {

			$this->ERROR['data']['message'] = $e->getMessage();
			return $this->ERROR;
		}
	}

	public function updatePost($post, $postid) {

		try {

			$stmt = $this->db->prepare("SELECT * FROM posts WHERE id=:postid LIMIT 1");
			$stmt->execute(array(':postid' => $postid));
			$original_post = $stmt->fetch(PDO::FETCH_ASSOC);

			if(!$original_post) {
				$this->ERROR['data']['message'] = 'Post does not exist';
				return $this->ERROR;
			}

			// Ensure some data is passed
			if(is_array($post)) {

				// Find differences between newly passed post and the original retreieved
				$diff = array_diff_key($original_post, $post); 
				// Merge differences into new array that contains old unchanged values, and new updated values
				$new_post = array_merge($diff, $post);
			}else {

				// If the user did NOT pass anything, simply use old data
				// Perhaps they just want to trigger a modified column update
				$new_post = $original_post;
			}

			

			// Create prepared statement for updating post
			$stmt = $this->db->prepare("UPDATE posts SET author=:author, content=:content WHERE id=:postid");
			$update = $stmt->execute(array(
				':author' => $new_post['author'], 
				':content' => $new_post['content'],
				':postid' => $postid
			));

			// If update failed for whatever reason
			if(!$update) {
				$this->ERROR['data']['message'] = 'Unable to update post';
				return $this->ERROR;
			}

			$stmt = $this->db->prepare("SELECT * FROM posts WHERE id=:postid LIMIT 1");
			$stmt->execute(array(':postid' => $postid));
			$updated_post = $stmt->fetch(PDO::FETCH_ASSOC);

			$this->OK['data'] = $updated_post;
			return $this->OK;

		}catch(PDOException $e) {

			$this->ERROR['data']['message'] = $e->getMessage();
			return $this->ERROR;
		}
	}

	public function deletePost($postid) {

		try {

			$stmt = $this->db->prepare("DELETE FROM posts WHERE id=:postid");
			$stmt->execute(array(':postid' => $postid));

			// No data is passed just success message
			return $this->OK;

		}catch(PDOException $e) {
			$this->ERROR['data']['message'] = $e->getMessage();
			return $this->ERROR;
		}
	}
}










