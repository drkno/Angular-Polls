(function () {
    'use strict';

    var itemsControllers = angular.module('pageControllers', ['chart.js', 'ui.bootstrap']);

    itemsControllers.controller('HeaderController', ['$scope', '$location',
        function ($scope, $location) {
            $scope.current = function (currLocation) {
                return $location.path().startsWith(currLocation);
            };
        }
    ]);

    itemsControllers.controller('PollsController', ['$scope', '$http',
        function ($scope, $http) {
            $scope.polls = undefined;
            var pollsRestUrl = 'index.php/services/polls/?callback=JSON_CALLBACK';
            $http.jsonp(pollsRestUrl)
                .success(function(data) {
                    $scope.polls = data;
                })
                .error(function(data, status, headers, config) {
                    console.log("Error loading list of polls. Status was " + status + ".");
                }
            );
        }
    ]);

    itemsControllers.controller('ItemListCtrl', ['$scope', 
        function ($scope) {
            $scope.items = itemList;
            $scope.author = 'Angus McGurkinshaw';
        }]);

    itemsControllers.controller('ItemDetailCtrl', ['$scope', '$routeParams',
        function($scope, $routeParams) {
            var itemId = $routeParams.itemId;
            // Look up item in "database"
            for (var i = 0; i < itemList.length; ++i) {
                if (itemList[i].id == itemId) {
                    $scope.item = itemList[i];
                }
            }
        }
    ]);

}());