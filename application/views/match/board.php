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
		var otherColour = "#00AAAA"; //0
		var userColour = "#AA00AA"; //1
		var empty = "#369";
		
		//TODO 
		//if this is the user who sends the invite, then start with 1, else, -1
		var userTurn = 1;
		
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
					//get JSON object for opponent user's moves
					var url = "<?= base_url() ?>board/makeMove";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='win') {
							//we have a winner!
							var pieces = data.pieces;
							var winner = data.winner;
							$.each(pieces, function(col, row){
								var id = row * 10 + col;
				 				$('#' + id).attr('style', 'border: 4px solid #FF0000');
							});
							alert(winner + "has won the game!");
						} 
						if (data && data.status=='tie') {
							//tie
							alert("Tie!");
						}
						if (data && data.status=='success') {
							//just another ordinary move
							//show it on table
							//redraw the board
							userTurn = userTurn * -1;
							
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
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/makeMove";
				var id = event.target.id;

				//location of click in 5*7 matrix
				var col = (id % 10);
				var row = (id - col) /10;

				//check to make sure column is not full already
				//i.e. td with id col does not have "free" as its text				
				var state = $('#' + col).text();
				$('#' + col).text(state);
				if ((userTurn == 1) && ($('#' + col).text() == state)){
					userTurn = userTurn * -1;
					for (var i = 5; i>=0 ; i--) {
						var num = col + i * 10;
						var state1 = $('#' + num).text();
						if (state1 == state){ // if it's free
							$('#' + num).text("1");
							$('#' + num).css('background-color', userColour);
							break; 
						}
					
					}
				//not sure if this correctly calls the makeMove function
				// $.post(url,arguments,function(data, newMove, jqXHR){
						
					// 	});

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
				echo '<td id="' . ($c * 10 + $r) .'" name="00000000"> &nbsp; </td>';
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

