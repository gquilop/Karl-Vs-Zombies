<?php
require_once('../functions/load_config.php');
require_once('../functions/quick_con.php'); 
$config = load_config('../settings/config.dat'); 
$sql = my_quick_con($config) or die("MySQL problem");

if(isset($_POST) && $_POST) {
	$create = (isset($_POST['create'])) ? $_POST['create'] : 0;
	$table_u = $config['user_table'];
	
	$ret = mysql_query("TRUNCATE TABLE $table_u");
	
	// create the oz
	$ret = mysql_query("INSERT INTO $table_u (id, fname, lname, state, kills, pic_path) VALUES ('OriginalZombie','Original','Zombie', -3, 0, 'images/default_user.jpg');");
	
	// create the rest of the players
	for($i = 0; $i < $create; $i++) {
		$fname = "first".$i;
		$lname = "last".$i;
		$username = "testuser".$i;
		$password = md5("testing");
		$email = "test@email".$i.".com";
		$oz_opt = (($i % 2) == 0) ? 1 : 0;
		$state = (($i % 2) == 0) ? 1 : -1;
		if(($i % 4) == 0 && $i > 0) {
			$state = 0;
		}
		
		$salt = $username.$password.$i;
		$key = strtoupper(md5($salt));
		$id = substr($key, 0, $config['id_length']);
		
		$ret = mysql_query("INSERT INTO $table_u (id, fname, lname, username, password, email, oz_opt, state, kills, pic_path) VALUES ('$id','$fname','$lname','$username','$password','$email',$oz_opt, $state, 0, 'images/default_user.jpg');");
	}
	
	$message = ($create > 0) ? $create." users created" : "";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Create me an army!</title>
</head>

<body>

<?php print $message; ?>

<form name="createUsersForms" action="" method="post">
<table>
<tr>
	<td>Number of users to create (this will drop all existing users from the table)</td>
	<td><input type="text" name="create" size="6" maxlength="6" value="" /></td>
</tr><tr>
	<td align="center" colspan="2">
	<input type="submit" value=" Create Them " />
	</td>
</tr>
</table>
</form>

</body>
</html>
