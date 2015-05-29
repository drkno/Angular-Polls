(function () {
    'use strict';

    var module = angular.module('pageControllers');

    module.controller('PollController', ['$scope', '$http', '$routeParams', '$location',
        function ($scope, $http, $routeParams, $location) {
            $scope.poll = undefined;
            $scope.answer = undefined;
            $scope.loaded = false;
            $scope.voteMessage = "";
            $scope.loadingMessage = "Loading...";

            $scope.performVote = function() {
                var voteRestUrl = 'index.php/services/votes/' + $scope.poll.id + '/' + $scope.answer;

                $http.post(voteRestUrl, "")
                    .success(function() {
                        $scope.voteMessage = "Thank you for voting on this poll, your vote has been received.";
                    })
                    .error(function(data, status, headers, config) {
                        console.log("Error voting on poll. Status was " + status + ".");
                        $scope.voteMessage = "An error occurred while submitting your vote.";
                    }
                );
            };

            $scope.loadPoll = function() {
                var pollRestUrl = 'index.php/services/polls/' + $routeParams.pollId
                    + '?callback=JSON_CALLBACK';

                $http.jsonp(pollRestUrl)
                    .success(function(data) {
                        $scope.loaded = true;
                        $scope.poll = data;
                    })
                    .error(function(data, status, headers, config) {
                        console.log("Error loading poll. Status was " + status + ".");
                        $scope.loadingMessage = "An error occurred while loading the poll. Does it exist?";
                    }
                );
            };

            $scope.navigate = function(location) {
                switch (location) {
                    case 'back': $location.path('/polls'); break;
                    case 'result': $location.path('/result/' + $scope.poll.id); break;
                    default: $location.path(location); break;
                }
            }

            $scope.reset = function() {
                $scope.answer = undefined;
                $scope.voteMessage = "";
                $scope.pollForm.$setPristine();
            }

            $scope.loadPoll();
        }
    ]);

}());