(function() {
	var app = angular.module('Microblog.Service', []);

	app.factory('Post', ['$resource', function($resource) {
		return $resource('api/posts/:postid', {}, {
			put: { method: 'PUT' }
		});
	}]);

})();