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

        /* function to receive move, id
         note: insertable is already checked on client side,
             so there is definitely space to insert
         insert move in the table with colour according to id
        
         if board has >= 8 pieces on it, check for any wins (1) or full table (2)
	 if so, update match_status accordingly
        
         check if a game has started and get id.
         insert into db match info:
          `user1_id` INT NOT NULL ,
          `user2_id` INT NOT NULL ,
          `u1_msg` TEXT NULL ,
          `u2_msg` TEXT NULL ,
          `board_state` BLOB NULL ,
          `match_status_id` INT NOT NULL

         if this was first insert of game, get insert id => store as $_SESSION[id]
        
         return new table array

        */	


	/* function win: check for any line of 4s
	horizontal algorithm:

	vertical algorithm:

	right up diagnoal:

	right down diagonal:
	*/
}
?>
