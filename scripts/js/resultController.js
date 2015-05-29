(function () {
    'use strict';

    var module = angular.module('pageControllers');

    module.controller('ResultController', ['$scope', '$http', '$routeParams',
        function ($scope, $http, $routeParams) {
            $scope.pollResults = undefined;
            var votesRestUrl = 'index.php/services/votes/' +
                $routeParams.pollId + '?callback=JSON_CALLBACK';
            $http.jsonp(votesRestUrl)
                .success(function(data) {
                    $scope.pollResults = data;
                })
                .error(function(data, status, headers, config) {
                    console.log("Error loading poll results. Status was " + status + ".");
                }
            );
        }
    ]);

}());