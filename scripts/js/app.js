(function () {
    'use strict';

    var pollsApp = angular.module('pollsApp', [
        'ngRoute',
        'itemsControllers'
    ]);

    pollsApp.config(['$routeProvider', function($routeProvider) {
        $routeProvider.
            when('/poll/:pollId', {
                templateUrl: 'templates/poll.html',
                controller: 'PollController'
            }).
            when('/items', {
                templateUrl: 'templates/item-list.html',
                controller: 'ItemListCtrl'
            }).
            when('/items/:itemId', {
                templateUrl: 'templates/item-detail.html',
                controller: 'ItemDetailCtrl'
            }).
            otherwise({
                redirectTo: '/items'
            });
      }]);
}());
