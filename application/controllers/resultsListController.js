(function () {
    'use strict';

    var module = angular.module('pageControllers');

    module.controller('ResultsListController', ['$scope', '$http',
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
}());