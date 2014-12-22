app.controller('contactCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error

    $scope.login = {};
    $scope.doContactUs = function (customer) {
        Data.post('sendemail', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
			$location.path('main');
        });
    }
});
