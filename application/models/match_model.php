<?php
class Match_model extends CI_Model {
	
	function getExclusive($id)
	{
		$sql = "select * from `match` where id=? for update";
		$query = $this->db->query($sql,array($id));
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Match');
		else
			return null;
	}

	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('match');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Match');
		else
			return null;
	}
	
	
	function insert($match) {
		return $this->db->insert('match',$match);
	}
	
	
	function updateMsgU1($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('u1_msg'=>$msg));
	}
	
	function updateMsgU2($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('u2_msg'=>$msg));
	}
	
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('match_status_id'=>$status));
	}


	// [server] after processing move, update db with board state
	// $board is passed in as an array! => need to process into blob
	function updateBoard($id, $board) {
		$this->db->where('id',$id);
		$bboard = serialize($board);
		return $this->db->update('match', array('board_state'=>$bboard));
	}


	/* get board state($id): just use get($id), then get the match state
	for users to call.
	returns the status of the board
	*/

// 	function win($board) {
// 		// check for horizontal
// 	}
        /* TODO: function win($board)
	> returns id of winning user if win, -1 otherwise
	> if there is a winning 4-pieces, return array(id, winning piece)

        check for any line of 4s
        horizontal algorithm:

        vertical algorithm:

        right up diagnoal:

        right down diagonal:
        */

}
?>
