<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

?>
<table>
<?php 
	if ($users) {
		foreach ($users as $user) {
			if ($user->id != $currentUser->id) {
?>		
			<tr>
			<td> 
			<?= anchor("arcade/invite?login=" . $user->login,$user->fullName()) ?> 
			</td>
			</tr>

<?php 	
			}
		}
	}
?>

</table>