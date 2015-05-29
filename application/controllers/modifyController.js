(function () {
    'use strict';

    var module = angular.module('pageControllers');

    module.controller('ModifyController', ['$scope', '$http', '$routeParams', '$location',
        function ($scope, $http, $routeParams, $location) {
            $scope.poll = {title:"",question:"",answers:[]};
            $scope.loadingMessage = "";
            $scope.loaded = true;
            $scope.editing = false;
            $scope.errorMessage = [];

            $scope.loadPoll = function() {
                if (typeof $routeParams.pollId === 'undefined') {
                    return; // we have opened the page
                }
                $scope.editing = true;
                $scope.loaded = false;

                var pollRestUrl = 'index.php/services/polls/' + $routeParams.pollId
                    + '?callback=JSON_CALLBACK';

                $http.jsonp(pollRestUrl)
                    .success(function(data) {
                        $scope.loaded = true;
                        $scope.poll = data;
                        $scope.validateForm();
                    })
                    .error(function(data, status, headers, config) {
                        console.log("Error loading poll. Status was " + status + ".");
                        $scope.loadingMessage = "An error occurred while loading the poll. Does it exist?";
                    }
                );
            };

            $scope.navigate = function(location) {
                switch (location) {
                    case 'back': $location.path('/results'); break;
                    default: $location.path(location); break;
                }
            };

            $scope.addAnswer = function() {
                if (typeof $scope.poll.answers === 'undefined') {
                    $scope.poll.answers = [];
                }
                $scope.poll.answers.push("");
                $scope.validateForm();
            };

            $scope.removeAnswer = function(index) {
                $scope.poll.answers.splice(index, 1);
                $scope.validateForm();
            };

            $scope.validateForm = function() {
                $scope.errorMessage = [];
                if (!$scope.poll.title || $scope.poll.title.length  < 1 || $scope.poll.title.length > 255) {
                    $scope.errorMessage.push("Title must be between 1 and 255 characters long.");
                }

                if (!$scope.poll.question || $scope.poll.question.length  < 1 || $scope.poll.question.length > 255) {
                    $scope.errorMessage.push("Question must be between 1 and 255 characters long.");
                }

                if (!$scope.poll.answers || $scope.poll.answers.length < 2) {
                    $scope.errorMessage.push("Poll must have at least two answers to be a valid poll.");
                    return;
                }

                for (var i = 0; i < $scope.poll.answers.length; i++) {
                    if (!$scope.poll.answers[i] || $scope.poll.answers[i].length  < 1
                        || $scope.poll.answers[i].length > 255) {
                        $scope.errorMessage.push("Answer " + (i+1) + " must be between 1 and 255 characters long.");
                    }
                }
            };

            $scope.save = function() {
                var modifyRestUrl = 'index.php/services/polls/' + ($scope.editing ? $routeParams.pollId : '');
                $scope.loaded = false;
                ($scope.editing?$http.put(modifyRestUrl, $scope.poll):$http.post(modifyRestUrl, $scope.poll))
                    .success(function() {
                        $scope.loaded = true;
                        $scope.navigate('back');
                    })
                    .error(function(data, status, headers, config) {
                        console.log("Error saving. Status was " + status + ".");
                        $scope.loadingMessage = "An error occurred while saving the poll.";
                    }
                );
            };
            $scope.loadPoll();
            $scope.validateForm();
        }
    ]);
}());