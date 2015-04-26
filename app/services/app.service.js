(function() {
	var app = angular.module('Microblog.Service', []);

	app.factory('Post', ['$resource', function($resource) {
		return $resource('api/post/:postid', {}, {
			put: { method: 'PUT' }
		});
	}]);

})();