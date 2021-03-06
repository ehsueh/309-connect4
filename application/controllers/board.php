<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

?>
<?php

class Board extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	
    		
    		if (!isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }
    
    
    function index() {
		$user = $_SESSION['user'];
    		    	
	    	$this->load->model('user_model');
	    	$this->load->model('invite_model');
	    	$this->load->model('match_model');
	    	
	    	$user = $this->user_model->get($user->login);

	    	$invite = $this->invite_model->get($user->invite_id);
	    	
	    	if ($user->user_status_id == User::WAITING) {
	    		$invite = $this->invite_model->get($user->invite_id);
	    		$otherUser = $this->user_model->getFromId($invite->user2_id);
	    		$userTurn = 1;
	    	}
	    	else if ($user->user_status_id == User::PLAYING) {
	    		$match = $this->match_model->get($user->match_id);
	    		if ($match->user1_id == $user->id) {
	    			$otherUser = $this->user_model->getFromId($match->user2_id);
	    			$userTurn = -1;
	    		}
	    		else {
	    			$otherUser = $this->user_model->getFromId($match->user1_id);
	    			$userTurn = 1;
	    		}
	    	}
	    	
	    	$data['user']=$user;
	    	$data['otherUser']=$otherUser;
	    	$data['userTurn']=$userTurn;
	    	
	    	switch($user->user_status_id) {
	    		case User::PLAYING:	
	    			$data['status'] = 'playing';
	    			break;
	    		case User::WAITING:
	    			$data['status'] = 'waiting';
	    			break;
	    	}
	    	
		$this->load->view('match/board',$data);
    }

 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}

 	//postBoard function or include in postMessage

 	//getBoard
	function getBoard() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
		$board = unserialize($match->board_state);
 		
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','board'=>$board));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}


 	// to check if the game is over
	function getStatus() {

		$this->load->model('user_model');
		$this->load->model('match_model');
		
		$user = $_SESSION['user'];
		$user = $this->user_model->get($user->login);
		if ($user->user_status_id != User::PLAYING) {
			$errormsg="Not in PLAYING state";
			goto error;
		}
		// start transactional mode
		//$this->db->trans_begin();
		
		$match = $this->match_model->getExclusive($user->match_id);
		$status_id = $match->match_status_id;
		$board = unserialize(base64_decode($match->board_state));
		$inviter = $match->user1_id;
		
		if ($status_id == 1) 	    // normal move
			echo json_encode(array('status'=>'active','board'=>$board, 'inviter'=>$inviter));
		else if ($status_id == 2)  // u1win
			echo json_encode(array('status'=>$match->user1_id,'board'=>$board, 'inviter'=>$inviter));
		else if ($status_id == 3)  // u2win
			echo json_encode(array('status'=>$match->user2_id,'board'=>$board, 'inviter'=>$inviter));
		else if ($status_id == 4)  // tie
			echo json_encode(array('status'=>'tie','board'=>$board, 'inviter'=>$inviter));
		return;
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
		
	}
 	
	
	// inserts move into board, and updates the board in the db
	// checks for win or tie; makes any necessary state changes
	function makeMove() {
		
		$this->load->model('user_model');
		$this->load->model('match_model');
		
		$user = $_SESSION['user'];
		$user = $this->user_model->get($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="Not in PLAYING state";
                goto error;
        }
        // start transactional mode
        $this->db->trans_begin();

        // get board and insert
       	$match = $this->match_model->getExclusive($user->match_id);
       	// if board was not initialized
       	if ($match->board_state == null) {
       		$col0 = array();
       		$col1 = array();
       		$col2 = array();
       		$col3 = array();
       		$col4 = array();
       		$col5 = array();
       		$col6 = array();
       		$board = array($col0, $col1, $col2, $col3, $col4, $col5, $col6);
       	}
       	// else if board was initialized
       	else {
			$board = unserialize(base64_decode($match->board_state));
       	}

		$col = $this->input->post('col');
       	array_push($board[$col], $user->id);
		
		$this->match_model->updateBoard($match->id, $board);
		
		// 7:   number of columns (nested arrays)
		// > 6: number needed for a win
		$count = count($board, COUNT_RECURSIVE);
		if ($count > 13) {

			// check for win
			$win = $this->match_model->win($board, $col);
			// if win, returns the array with winning pieces --> != -1
			// if no win, returns -1 
			if ($win != -1) {
				$winner = $user->id;
				$pieces = $win[1]; // array(col=>row, col=>row, col=>row, col=>row)
				// process the json with: http://www.w3schools.com/Php/php_arrays.asp

				// update match status
				if ($match->user1_id == $winner) {
					$this->match_model->updateStatus($user->match_id, Match::U1WIN);
				}
				else {
					$this->match_model->updateStatus($user->match_id, Match::U2WIN);
				}
// 				echo json_encode(array('status'=>'win', 
// 				    'winner'=>$winner, 'pieces'=>$pieces));
			}

			// check for tie: board is full: 7 + 42 = 49
			else if ($count == 49) {
				$this->match_model->updateStatus($user->match_id, Match::TIE);
				// echo json_encode(array('status'=>'tie'));
			}
		}

		// if we've reached here, it's just a normal move
// 		else {
// 			// don't bother updating status in database
// 			echo json_encode(array('status'=>'success'));
// 		}

        if ($this->db->trans_status() === FALSE) {
                $errormsg = "Transaction error";
                goto transactionerror;
        }

        // if all went well commit changes
        $this->db->trans_commit();
        return;

        transactionerror:
        $this->db->trans_rollback();

        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));	
	}
}


