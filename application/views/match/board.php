
<!DOCTYPE html>

<html>
	
	<head>

	<link rel="stylesheet" type="text/css" href="<?php  echo base_url(); ?>/css/template.css">

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		var otherColour = "#00AAAA"; //2
		var userColour = "#AA00AA"; //1
		var empty = "#369";
		var board = "";
		var userTurn = "<?= $userTurn ?>";
		alert("userTurn before anything: " + userTurn);
		$(function(){

			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to play was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'playing';
									$('#status').html('Playing ' + otherUser);
								}
								
						});
					}

					var url = "<?= base_url() ?>board/getMsg";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
			
						}
					});

					// update board: we're still in the auto-check!
					var url = "<?= base_url() ?>board/getStatus";
					$.getJSON(url, function (data,text,jqXHR){
						//if (data && data.status=='success') {
						// if user == inviter, treat the first case special:
						// when board == "" and board != data.board,
						//     then ONLY update board
						
						if (user == data.inviter && board == "" && board != data.board) {
							board = data.board.toString();
						}
						
						if (board.toString() != data.board.toString() && board != "") { // the other user made a move

							alert("58: board is: " + board.toString()); 
							// ,,1,2,2,,2,2,2,2
							// ,,1,2,2,,2,2,2,2
							alert("data board is: " + data.board.toString());
							
							userTurn *= -1;
							board = data.board.toString(); // TODO: make sure this works
						    for (var r = 0; r<6; r++) {
							    for (var c = 0; c<7; c++){ 
								    $('#' + ((r-5)*c)).text(data.board[r][c]); 
								    if (data.board[r][c] == 1) $('#' + ((r-5)*c)).css('style', userColour); 
								    if (data.board[r][c] == 2) $('#' + ((r-5)*c)).css('style', otherColour);
							    }
						    }
						}
						
					});

			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/postMsg";
				
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});

				// clear textbox after sending message
				// doesn't work yet
				// $('[name=msg]').val("");

				return false;
				
				});	

			$('td').click(function(event){
				var url = "<?= base_url() ?>board/makeMove";
				var id = event.target.id;

				alert("userTurn: " + userTurn);
				//location of click in 5*7 matrix
				var col = (id % 10);
				var num = 0;
				
				//check to make sure column is not full already
				//i.e. td with id col does not have "free" as its text				
				var state = $('#' + col).text();
				$('#' + col).text(state);
				if ((userTurn == 1) && ($('#' + col).text() == state)){
					for (var i = 5; i>=0 ; i--) {
						num = col + i * 10;
						var state1 = $('#' + num).text();
						if (state1 == state){ // if it's free
							$('#' + num).text("1");
							$('#' + num).css('background-color', userColour);
							break; 
						}
					
					}
					
					// draw, update status
					var arguments = {"col": col};
					$.post(url, arguments, function(data, text, jqXHR) {
						var url = "<?= base_url() ?>board/getStatus";
						$.getJSON(url, function(data, text, jqXHR) {
							//alert("in getJSON; got some status back");

							alert("128: board is: " + data.board);
							userTurn *= -1;
							board = data.board.toString();
							
							// win
							if (data && typeof(data.status) == "number") {
								alert("data status is " + data.status);
								endgame = true;
								var pieces = data.pieces;
								var winner = data.winner;
								$.each(pieces, function(col, row){
									var id = row * 10 + col;
					 				$('#' + id).attr('style', 'border: 4px solid #FF0000');
								});
								alert(winner + "has won the game!");
								window.location.href = '<?= base_url() ?>arcade/index';
							}
							// tie
							else if (data && data.status=='tie') {
								// alert("data status is " + data.status);
								alert("Tie!");
								window.location.href = '<?= base_url() ?>arcade/index';
							}
							//normal move
							else if (data && data.status=='active') {
								// alert("data status is " + data.status);
								$('#' + num).text("1");
							}
							else
								alert("in else: data status is " + data.status);
						});
					});
				

				}

			});
				    
		});
	
	</script>
	</head> 
<body>  
	<h1>Game Area</h1>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  

	</div>
	
	<div id='status'> 
	<?php 
		if ($status == "playing")
			echo "Playing " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div>
	<div  id='board'>
	<table>
		
	<?php 
		for ($c = 0; $c <6; $c++){
			echo '<tr>';
			for ($r = 0; $r <7; $r++){
				//id represents position
				//name stores info about neighbourhood
				echo '<td id="' . ($c * 10 + $r) .'" name="00000000"> free </td>';
			}
			echo '</tr>';
		}
	?>
	</table>
	</div>
	 
<?php 

	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
	
	
	
	
</body>

</html>
