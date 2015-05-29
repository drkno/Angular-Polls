(function () {
    'use strict';

    var pollsApp = angular.module('pollsApp', [
        'ngRoute',
        'pageControllers'
    ]);

    pollsApp.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
            when('/polls', {
                templateUrl: 'application/views/polls.html',
                controller: 'PollsListController'
            }).
            when('/poll/:pollId', {
                templateUrl: 'application/views/poll.html',
                controller: 'PollController'
            }).
            when('/results', {
                templateUrl: 'application/views/results.html',
                controller: 'ResultsListController'
            }).
            when('/result/:pollId', {
                templateUrl: 'application/views/result.html',
                controller: 'ResultController'
            }).
            when('/modify', {
                templateUrl: 'application/views/modify.html',
                controller: 'ModifyController'
            }).
            when('/modify/:pollId', {
                templateUrl: 'application/views/modify.html',
                controller: 'ModifyController'
            }).
            when('/about', {
                templateUrl: 'application/views/about.html'
            }).
            otherwise({
                redirectTo: '/polls'
            });
      }]);
}());
