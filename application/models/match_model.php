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
	
	// update the status of the match with id $id and status $status
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('match_status_id'=>$status));
	}


	// [server] after processing move, update db with board state
	// $board is passed in as an array! => need to process into blob
	function updateBoard($id, $board) {
		$this->db->where('id',$id);
		$bboard = base64_encode(serialize($board));
		return $this->db->update('match', array('board_state'=>$bboard));
	}


	/* get board state($id): just use get($id), then get the match state
	for users to call.
	returns the status of the board
	*/

	function horizontal($board, $col) {
		// get index of last item in column
		// check same index in other rows
		// depending on which column move is in, check 
	}

	//modifying the classic connect4 algo
	//taking  a bitboard from user
	function win2($bitboard, $lastmoveid) {
		if ($lastmoveid && $lastmove){};
		// NOTE: only need to ceck anything that contains the last item of $col

		// check for horizontal
	}
	
	
	function win($board, $x) {
		// last piece was inserted at $x, $y
		$y = count($board[$x]) - 1;
		// last piece was inserted by this user
		$user = $board[$x][$y];
		
		// vertical: check only if $y > 2
		if ($y >= 3) {
			// only check from this piece down
			if (($board[$x][$y] == $user) && ($board[$x][$y-1] == $user) &&
				($board[$x][$y-2] == $user) && ($board[$x][$y-3] == $user)) {
				return array($x=>$y-3, $x=>$y-2, $x=>$y-1, $x=>$y);
			}
		}
		
		// horizontal: 4 consecutive elements are $user
		for ($col = 0; $col < 4; $col++) {
			if (($board[$col][$y] == $user) && 
					(count($board[$col+1]) > $y) && ($board[$col+1][$y] == $user) &&
					(count($board[$col+2]) > $y) && ($board[$col+2][$y] == $user) &&
					(count($board[$col+3]) > $y) && ($board[$col+3][$y] == $user)) {
				return array($col=>$y, $col+1=>$y, $col+2=>$y, $col+3=>$y);
			}		$dr = array(
			array( array(0, 3), array(1, 2), array(2, 1), array(3, 0) ), 
			array( array(0, 4), array(1, 3), array(2, 2), array(3, 1), array(4, 0) ), 
			array( array(0, 5), array(1, 4), array(2, 3), array(3, 2), array(4, 1), 
					array(5, 0) ), 
			array( array(1, 5), array(2, 4), array(3, 3), array(4, 2), array(5, 1), 
					array(6, 0)), 
			array( array(2, 5), array(3, 4), array(4, 3), array(5, 2), array(6, 1) ), 
			array( array(3, 5), array(4, 4), array(5, 3), array(6, 2) ) );
		
		// for each diagonal right, diagonal
		for ($diag = 0; $diag < 6; $diag++) {
			// if this move is in the diagonal
			if (in_array(array($x, $y), $dr[$diag])) {
				// check if it's part of a 4-piece
				for ($i=0; $i <= count($dr[$diag]) - 4; $i++) {
					$drx1 = $dr[$diag][$i][0];
					$dry1 = $dr[$diag][$i][1];
					$drx2 = $dr[$diag][$i+1][0];
					$dry2 = $dr[$diag][$i+1][1];
					$drx3 = $dr[$diag][$i+2][0];
					$dry3 = $dr[$diag][$i+2][1];
					$drx4 = $dr[$diag][$i+3][0];
					$dry4 = $dr[$diag][$i+3][1];
					if ( 	(count($board[$drx1]) > $dry1) && ($board[$drx1][$dry1] == $user) &&
							(count($board[$drx2]) > $dry2) && ($board[$drx2][$dry2] == $user) &&
							(count($board[$drx3]) > $dry3) && ($board[$drx3][$dry3] == $user) &&
							(count($board[$drx4]) > $dry4) && ($board[$drx4][$dry4] == $user) ) {
						return array($drx1=>$dry1, $drx2=>$dry2, $drx3=>$dry3, $drx4=>$dry4);
					}
				}
			} 
		}
		}
		
		// diagonal right: \
		$dr = array(
			array( array(0, 3), array(1, 2), array(2, 1), array(3, 0) ), 
			array( array(0, 4), array(1, 3), array(2, 2), array(3, 1), array(4, 0) ), 
			array( array(0, 5), array(1, 4), array(2, 3), array(3, 2), array(4, 1), 
					array(5, 0) ), 
			array( array(1, 5), array(2, 4), array(3, 3), array(4, 2), array(5, 1), 
					array(6, 0)), 
			array( array(2, 5), array(3, 4), array(4, 3), array(5, 2), array(6, 1) ), 
			array( array(3, 5), array(4, 4), array(5, 3), array(6, 2) ) );
		
		// for each diagonal right diagonal
		for ($diag = 0; $diag < 6; $diag++) {
			// if this move is in the diagonal
			if (in_array(array($x, $y), $dr[$diag])) {
				// check if it's part of a 4-piece
				for ($i=0; $i <= count($dr[$diag]) - 4; $i++) {
					$drx1 = $dr[$diag][$i][0];
					$dry1 = $dr[$diag][$i][1];
					$drx2 = $dr[$diag][$i+1][0];
					$dry2 = $dr[$diag][$i+1][1];
					$drx3 = $dr[$diag][$i+2][0];
					$dry3 = $dr[$diag][$i+2][1];
					$drx4 = $dr[$diag][$i+3][0];
					$dry4 = $dr[$diag][$i+3][1];
					if ( 	(count($board[$drx1]) > $dry1) && ($board[$drx1][$dry1] == $user) &&
							(count($board[$drx2]) > $dry2) && ($board[$drx2][$dry2] == $user) &&
							(count($board[$drx3]) > $dry3) && ($board[$drx3][$dry3] == $user) &&
							(count($board[$drx4]) > $dry4) && ($board[$drx4][$dry4] == $user) ) {
						return array($drx1=>$dry1, $drx2=>$dry2, $drx3=>$dry3, $drx4=>$dry4);
					}
				}
			} 
		}
		
		
		// diagonal left: /
		$dl = array(
				array( array(0, 2), array(1, 3), array(2, 4), array(3, 5) ),
				array( array(0, 1), array(1, 2), array(2, 3), array(3, 4), array(4, 5) ),
				array( array(0, 0), array(1, 1), array(2, 2), array(3, 3), array(4, 4), 
						array(5, 5) ),
				array( array(1, 0), array(2, 1), array(3, 2), array(4, 3), array(5, 4), 
						array(6, 5) ),
				array( array(2, 0), array(3, 1), array(4, 2), array(5, 3), array(6, 4) ),
				array( array(3, 0), array(4, 1), array(5, 2), array(6, 3) ) );
		
		// for each diagonal left, diagonal
		for ($diag = 0; $diag < 6; $diag++) {
			// if this move is in the diagonal
			if (in_array(array($x, $y), $dl[$diag])) {
				// check if it's part of a 4-piece
				for ($i=0; $i <= count($dl[$diag]) - 4; $i++) {
					$dlx1 = $dl[$diag][$i][0];
					$dly1 = $dl[$diag][$i][1];
					$dlx2 = $dl[$diag][$i+1][0];
					$dly2 = $dl[$diag][$i+1][1];
					$dlx3 = $dl[$diag][$i+2][0];
					$dly3 = $dl[$diag][$i+2][1];
					$dlx4 = $dl[$diag][$i+3][0];
					$dly4 = $dl[$diag][$i+3][1];
					if ( 	(count($board[$dlx1]) > $dly1) && ($board[$dlx1][$dly1] == $user) &&
							(count($board[$dlx2]) > $dly2) && ($board[$dlx2][$dly2] == $user) &&
							(count($board[$dlx3]) > $dly3) && ($board[$dlx3][$dly3] == $user) &&
							(count($board[$dlx4]) > $dly4) && ($board[$dlx4][$dly4] == $user) ) {
						return array($dlx1=>$dly1, $dlx2=>$dly2, $dlx3=>$dly3, $dlx4=>$dly4);
					}
				}
			}
		}
		
		return -1;
	}

}
?>
