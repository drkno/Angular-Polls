<?php
/*
 * The Poll class, representing a single poll.
 */
class Poll extends CI_Model {
    public $id;
    public $title;
	public $question;
    public $answers;

    /**
     * Imports data from an array to this Poll instance.
     * @param $array array to import.
     */
    public function fromArray($array) {
        foreach ($array as $field => $value) {
            $this->$field = $value;
        }
    }

    /**
     * Gets a Poll with a specific ID.
     * @param $id ID of the poll to get.
     * @return Poll an instance of the poll.
     * @throws Exception if the poll does not exist.
     */
	public static function read($id) {
		$CI =& get_instance();
		$CI->load->database();
		
		$query = $CI->db->get_where('polls', array('id'=>$id));
		if ($query->num_rows !== 1) {
            throw new Exception("Poll does not exist.");
        }
		$rows = $query->result_array();
		$row = $rows[0];
		$poll = new Poll();
		$poll->id = $row['id'];
		$poll->title = $row['title'];
		$poll->question = $row['question'];
		$poll->answers = array_values(Poll::readAnswers($id));
		
		return $poll;
	}

    /**
     * Gets the answers for a poll.
     * @param $id ID of the poll to get answers for.
     * @return array the answers of the poll.
     * @throws Exception if the poll has no answers.
     */
	private static function readAnswers($id) {
		$CI =& get_instance();
		$CI->load->database();

        $CI->db->select('*');
        $CI->db->from('answers');
        $CI->db->where('poll', $id);
        $CI->db->order_by('id', 'asc');

        $result = $CI->db->get()->result_array();

		if (count($result) <= 1) {
            throw new Exception("Poll has no answers, or not enough answers.");
        }
		$answers = array();
		foreach ($result as $row) {
			$answers[$row['id']] = $row['answer'];
		}
		return $answers;
	}

    /**
     * Gets a list of polls.
     * @return array a list of polls.
     */
    public static function allPolls() {
		$CI =& get_instance();
		$CI->load->database();
		
		$CI->db->select('id, title');
		$CI->db->from('polls');
		$polls = array();
		foreach ($CI->db->get()->result() as $poll) {
			$polls[] = $poll;
		}
		return $polls;
	}

    /**
     * Validates that a poll meets the required criteria.
     * @param $poll poll to validate.
     * @throws Exception if the poll is invalid.
     */
    private static function validatePoll($poll) {
        if (is_null($poll)) {
            throw new Exception("Poll cannot be null.");
        }

        if (!is_string($poll->title) || $poll->title == "" ||
            strlen($poll->title) >= 255) {
            throw new Exception("Title cannot be empty or longer than 255 characters.");
        }

        if (!is_string($poll->question) || $poll->question == "" ||
            strlen($poll->question) >= 255) {
            throw new Exception("Question cannot be empty or longer than 255 characters.");
        }

        if (!is_array($poll->answers) || $poll->answers == false
            || count($poll->answers) < 2) {
            throw new Exception("Poll must have at least 2 answers.");
        }

        foreach ($poll->answers as $answer) {
            if (!is_string($poll->title) ||
                strlen($answer) <= 0 || strlen($answer) >= 255) {
                throw new Exception("Answer must have between 1 and 255 characters.");
            }
        }
    }

    /**
     * Create a new poll.
     * @param $poll poll data to use when creating a poll.
     * @return int id of the new poll.
     * @throws Exception if data was invalid or creation failed.
     */
	public static function createPoll($poll) {
        $CI =& get_instance();
        $CI->load->database();

        Poll::validatePoll($poll);

        $pollData = array(
            'title' => $poll->title,
            'question' => $poll->question
        );
        $CI->db->insert('polls', $pollData);
        $id = $CI->db->insert_id();

        Poll::addAnswers($CI, $id, $poll->answers);

        return $id;
    }

    /**
     * Updates an existing poll with new data.
     * Data must be complete as with creating a new poll.
     * Note: will delete existing votes from the poll.
     * @param $id ID of the poll to update.
     * @param $poll poll data.
     * @throws Exception if updating failed because of invalid data.
     */
    public static function updatePoll($id, $poll) {
        $CI =& get_instance();
        $CI->load->database();

        Poll::validatePoll($poll);
        Poll::deleteVotes($id);
        Poll::deleteAnswers($CI, $id);
        Poll::addAnswers($CI, $id, $poll->answers);

        $pollData = array(
            'title' => $poll->title,
            'question' => $poll->question
        );
        $CI->db->where('id', $id);
        $CI->db->update('polls', $pollData);
    }

    /**
     * Adds answers to a poll.
     * @param $CI database instance.
     * @param $id ID of the poll to add answers to.
     * @param $answers Array of answers to add.
     */
    private static function addAnswers($CI, $id, $answers) {
        $answersData = array();
        foreach ($answers as $answer) {
            $answersData[] = array(
                'poll' => $id,
                'answer' => $answer
            );
        }
        $CI->db->insert_batch('answers', $answersData);
    }

    /**
     * Deletes answers from a poll.
     * @param $CI Database instance.
     * @param $id ID of the poll to delete answers from.
     */
    private static function deleteAnswers($CI, $id) {
        $CI->db->delete("answers", array('poll' => $id));
    }

    /**
     * Delete a poll.
     * @param $pollId ID of the poll to delete.
     * @throws Exception if the poll does not exist.
     */
    public static function deletePoll($pollId) {
        $CI =& get_instance();
        $CI->load->database();

        Poll::deleteAnswers($CI, $pollId);
        $CI->db->delete("polls", array('id' => $pollId));
        $rowsChanged = intval($CI->db->affected_rows());
        if ($rowsChanged < 1) {
            // Safe to check after cause if it didn't exist, nothing would happen.
            throw new Exception("Nothing to delete.");
        }
    }

    /**
     * Vote on a poll.
     * @param $pollId ID of the poll to vote on.
     * @param $answerNum number of the answer to vote on.
     * @param $ip IP of the voter (IPv4/IPv4 Teredo/IPv6).
     * @throws Exception If poll or answer does not exist,
     *         or if IP address is longer than 50 characters.
     */
	public static function vote($pollId, $answerNum, $ip) {
		$CI =& get_instance();
		$CI->load->database();
		
		$answers = array_keys(Poll::readAnswers($pollId));
		
		if ($answerNum < 0 || $answerNum >= count($answers)) {
			throw new Exception('Poll answer does not exist.');
		}
		
		$answerId = $answers[$answerNum];
		$data = array(
			'answer' => $answerId,
			'ip' => $ip
		);
		$CI->db->insert('votes', $data);
	}

    /**
     * Gets all the votes on a particular poll.
     * @param $pollId poll to get votes on.
     * @return array votes and answers.
     */
	public static function getVotes($pollId) {
        // Query
		$CI =& get_instance();
		$CI->load->database();
		$CI->db->select('answers.answer as answer, count(votes.id) as votes');
		$CI->db->from('answers');
		$CI->db->join('votes', 'answers.id=votes.answer', 'left');
		$CI->db->where('answers.poll', $pollId);
		$CI->db->group_by('answers.id');
		$CI->db->order_by('answers.id', 'asc');
		$result = $CI->db->get()->result_array();

		return $result;
	}

    /**
     * Deletes votes on a poll.
     * @param $pollId poll to delete votes from.
     * @throws Exception thrown when an invalid ID is provided.
     */
	public static function deleteVotes($pollId) {
		$CI =& get_instance();
		$CI->load->database();
		
		$answers = array_keys(Poll::readAnswers($pollId));

		foreach ($answers as $answerId) {
			$CI->db->delete("votes", array('answer' => $answerId));
		}
	}
};