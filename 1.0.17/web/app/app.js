var app = angular.module('kameti', ['ngRoute', 'ngAnimate', 'toaster','dcbImgFallback']);

app.config(['$routeProvider',
  function ($routeProvider) {
        $routeProvider.
        when('/main', {
            title: 'Kameti',
            templateUrl: 'web/html/main.html'
        })
            .when('/about', {
                title: 'About',
                templateUrl: 'web/html/about.html'
            })

            .when('/contact', {
                title: 'Contact',
                templateUrl: 'web/html/contact.html',
                controller: 'contactCtrl'
            })

            .when('/price', {
                title: 'Contact',
                templateUrl: 'web/html/price.html'
            })
            .when('/faq', {
                title: 'Contact',
                templateUrl: 'web/html/faq.html'
            })
            .when('/feature', {
                title: 'About',
                templateUrl: 'web/html/feature.html'
            })
            .when('/privacy', {
                title: 'Privacy',
                templateUrl: 'web/html/privacy.html'
            })

            .when('/terms', {
                title: 'Terms',
                templateUrl: 'web/html/terms.html'
            })
			
			.when('/help', {
                title: 'Help',
                templateUrl: 'web/html/help.html'
            })
			
            .when('/login', {
                title: 'Login',
                templateUrl: 'web/html/login.html',
                controller: 'authCtrl'
            })
            .when('/logout', {
                title: 'Kameti',
                templateUrl: 'web/html/logout.html',
                controller: 'authCtrl'
            })
            .when('/', {
                title: 'Kameti',
                templateUrl: 'web/html/main.html'
            })
            .otherwise({
                redirectTo: '/main'
            });
  }]);
