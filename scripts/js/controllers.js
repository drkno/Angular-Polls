(function () {
    'use strict';

    var itemList = [
        {id: 1, name: 'Knurled widget', stock: 23},
        {id: 2, name: 'Sky hook', stock: 1},
        {id: 3, name: 'Feathered codpiece', stock: 111}
    ];

    var itemsControllers = angular.module('itemsControllers', []);

    itemsControllers.controller('PollController', ['$scope', '$http', '$routeParams',
        function ($scope, $http, $routeParams) {
            $scope.poll = undefined;
            var pollRestUrl = 'index.php/services/polls/' + $routeParams.pollId
                + '?callback=JSON_CALLBACK';

            $http.jsonp(pollRestUrl)
                .success(function(data) {
                    $scope.poll = data;
                })
                .error(function(data, status, headers, config) {
                    console.log("Error loading poll. Status was " + status + ".");
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