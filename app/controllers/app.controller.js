(function() {
	var app = angular.module('Microblog.Controller', []);

	app.controller('AppController', ['$scope', 'Post', function($scope, Post) {
		$scope.posts = [];
		$scope.post = {};
		$scope.edit_post = {};

		Post.query(function(response) {
			$scope.posts = response;
		});

		$scope.create = function(post) {
			Post.save(post, function(response) {
				if(response.status == 200) {
					$scope.posts.unshift(response.data);
					$scope.post = {};
				}else{
					console.log(response.data.message);
				}
			});
		};

		$scope.remove = function(post) {
			Post.remove({postid: post.id}, function(response) {
				if(response.status == 200) {
					var index = $scope.posts.indexOf(post);
					$scope.posts.splice(index, 1);
				}
			});
		};

		// Used to save state of post, if user decided to abandon changes
		$scope.revert = function(post) {

			$scope.edit_post = angular.copy(post);
		};

		$scope.update = function(post, original_post) {
			Post.put({postid: post.id}, post, function(response) {
				if(response.status == 200) {
					var index = $scope.posts.indexOf(original_post);
					$scope.posts[index] = response.data;

				}else{
					console.log(response.data.messasge);
				}
			});
		};

	}]);

})();