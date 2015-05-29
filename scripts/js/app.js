(function () {
    'use strict';

    var pollsApp = angular.module('pollsApp', [
        'ngRoute',
        'pageControllers'
    ]);

    pollsApp.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
            when('/polls', {
                templateUrl: 'templates/polls.html',
                controller: 'PollsListController'
            }).
            when('/poll/:pollId', {
                templateUrl: 'templates/poll.html',
                controller: 'PollController'
            }).
            when('/results', {
                templateUrl: 'templates/results.html',
                controller: 'ResultsListController'
            }).
            when('/result/:pollId', {
                templateUrl: 'templates/result.html',
                controller: 'ResultController'
            }).
            otherwise({
                redirectTo: '/polls'
            });
      }]);
}());
