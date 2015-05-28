<?php

if (!defined('BASEPATH')) {
	exit('Direct access to this file is not allowed.');
}

require_once(APPPATH."libraries/REST_Controller.php");

/**
 * REST services controller for the
 * polls and voting system.
 */
class Services extends REST_Controller
{
	/**
	 * Gets a list of polls, with ID and title attributes.
	 * Responds with a 200 (OK) and a JSON list of polls.
	 */
	private function polls_getall()
	{
		try {
			$this->load->model('poll');
			$polls = Poll::allPolls();
			$this->response($polls, 200);
		}
		catch (Exception $e) {
			$this->response(NULL, 404);
		}
	}
	
	/**
	 * Gets details about a specific poll based on a poll ID.
	 * Responds with a 200 (OK) and poll details if successful
	 * or a 404 (Not Found) if an error occurs.
	 * @param $id int - the ID of the poll to get.
	 */
	private function polls_getid($id)
	{
		try {
			$this->load->model('poll');
			$answers = Poll::read($id);
			$this->response($answers, 200);
		}
		catch (Exception $e) {
			$this->response(NULL, 404);
		}
	}
	
	/**
	 * Handles a get request to the polls service URL.
	 * @param $id int - (default = NULL) the ID of a poll
	 * to get details of.
	 */
	public function polls_get($id = NULL)
	{
		if ($id !== NULL) {
			$this->polls_getid($id);
		}
		else {
			$this->polls_getall();
		}
	}
	
	/**
	 * Creates a new poll based on provided POST data.
	 * If valid data is provided responds with a 201 (Created)
	 * and a Location header linking to the new poll. On an
	 * error it will return a 404 (Not Found).
	 * Expects valid poll data as POST data.
     * Note: API optional for assignment.
	 */
	public function polls_post()
	{
        try {
            $this->load->model('poll');
            $jsonData = json_decode(file_get_contents('php://input'));
            $poll = new Poll();
            $poll->fromArray($jsonData);
            $id = Poll::createPoll($poll);
            $CI =& get_instance();
            $url = $CI->config->site_url($CI->uri->uri_string());
            header("Location: $url/$id");
            $this->response(NULL, 201);
        }
        catch (Exception $e) {
            $this->response(NULL, 404);
        }
	}
	
	/**
	 * Replaces an existing poll at an ID based on provided PUT
	 * data. If valid data is provided, responds with a 200 (OK)
	 * otherwise responds with a 404 (Not Found).
	 * Expects valid poll data as PUT data.
     * Note: API optional for assignment.
     *
	 * @param $id int - ID of the poll to replace.
	 */
	public function polls_put($id = NULL)
	{
        try {
            $this->load->model('poll');
            $jsonData = json_decode(file_get_contents('php://input'));
            $poll = new Poll();
            $poll->fromArray($jsonData);
            Poll::updatePoll($id, $poll);
            $this->response(NULL, 200);
        }
        catch (Exception $e) {
            $this->response(NULL, 404);
        }
	}
	
	/**
	 * Deletes an existing poll at an ID.
	 * If deletion was successful, responds with a 200 (OK),
	 * otherwise it responds with a 404 (Not Found).
     * Note: API optional for assignment.
     *
	 * @param $id int - ID of the poll to delete.
	 */
	public function polls_delete($id)
	{
        try {
            $this->load->model('poll');
            Poll::deletePoll($id);
            $this->response(NULL, 200);
        }
        catch (Exception $e) {
            $this->response(NULL, 404);
        }
	}
	
	/**
	 * Performs a vote on a poll. If the vote was successful,
	 * responds with a 200 (OK), otherwise responds with a
	 * 404 (Not Found). Does not discriminate based on IP for
	 * multiple votes.
	 * @param $pollId int - ID of the poll to vote on.
	 * @param $vote int - Number of the answer to vote on.
	 */
	public function votes_post($pollId, $vote)
	{
		try {
			$this->load->model('poll');
			Poll::vote($pollId, $vote, $this->input->ip_address());
			$this->response(NULL, 200);
		}
		catch (Exception $e) {
			$this->response(NULL, 404);
		}
	}
	
	/**
	 * Gets the number of votes on a poll.
	 * If retrieval was successful, responds with a 200 (OK),
	 * otherwise responds with a 404 (Not Found).
	 * @param $pollId int - the poll to get the number of votes for.
	 */
	public function votes_get($pollId = NULL)
	{
		try {
			$this->load->model('poll');
			$votes = Poll::getVotes($pollId);
			$this->response($votes, 200);
		}
		catch (Exception $e) {
			$this->response(NULL, 404);
		}
	}
	
	/**
	 * Deletes all votes on a poll.
	 * Responds with a 200 (OK) if deletion was successful,
	 * otherwise responds with a 404 (Not Found).
	 * @param $pollId int - the poll ID to delete votes on.
	 */
	public function votes_delete($pollId = NULL)
	{
		try {
			$this->load->model('poll');
			Poll::deleteVotes($pollId);
			$this->response(NULL, 200);
		}
		catch (Exception $e) {
			$this->response(NULL, 404);
		}
	}
}