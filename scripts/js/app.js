(function () {
    'use strict';

    /* App Module */

    var itemsApp = angular.module('pollsApp', [
      'ngRoute',
      'itemsControllers'
    ]);

    itemsApp.config(['$routeProvider',
      function($routeProvider) {
        $routeProvider.
          when('/items', {
            templateUrl: 'partials/item-list.html',
            controller: 'ItemListCtrl'
          }).
          when('/items/:itemId', {
            templateUrl: 'partials/item-detail.html',
            controller: 'ItemDetailCtrl'
          }).
          otherwise({
            redirectTo: '/items'
          });
      }]);
}())
