<?php
ob_start();
session_start();
require_once('functions/load_config.php');
require_once('functions/quick_con.php'); 
$config = load_config('settings/config.dat');
$table_u = $config['user_table'];
$table_v = $config['var_table'];
$table_t = $config['time_table'];
$sql = my_quick_con($config) or die("MySQL problem");
// Set default time zone
$ret = mysql_query("SELECT zone FROM $table_t");
while($row = mysql_fetch_array($ret)) {
	date_default_timezone_set($row['zone']);
}

// update the starvation times and oz reveal (every 5 minutes, not on every page refresh)
$now = time();
$last_starvation_update = (isset($_SESSION['last_starvation_update'])) ? $_SESSION['last_starvation_update'] : $now;
if($last_starvation_update >= $now + 900) {
	$ret = mysql_query("UPDATE $table_u SET state = -4 WHERE now() > feed + INTERVAL 2 day;"); 
	$ret = mysql_query("UPDATE $table_u SET starved = feed + INTERVAL 2 day WHERE state = -4;");
	$ret = mysql_query("UPDATE $table_u SET state = 0 WHERE state = -4;");
	$ret = mysql_query("SELECT value FROM $table_v WHERE keyword='oz-revealed';");
	$reveal_oz = mysql_fetch_assoc($ret);
	$reveal_oz = $reveal_oz['value'];
	
	$_SESSION['last_starvation_update'] = time();
	$_SESSION['oz_revealed'] = $reveal_oz;	
} else {
	$reveal_oz = (isset($_SESSION['oz_revealed'])) ? $_SESSION['oz_revealed'] : 0;
}

$state_translate = array('-3'=>'horde', '-2'=>'horde (original)', '-1'=>'horde', '0'=>'deceased', '1'=>'resistance', '2'=>'resistance');
$admin = 0;
if(isset($_SESSION['pass_hash'])) $admin = 1;

if($_POST['mode'] == 'refresh') {
	$post_faction_array = array('a'=>'1 = 1', 'r'=>'state > 0', 'h'=>'state < 0', 'd'=>'state = 0'); 
	if(!$reveal_oz) { 
		$post_faction_array['r'] = 'state > 0 OR state = -2';
		$post_faction_array['h'] = 'state = -1 OR state = -3';
	}
	$post_sort_by_array = array('ln'=>'lname', 'fn'=>'fname', 'ks'=>'kills', 'kd'=>'killed', 'fd'=>'feed', 'sd'=>'starved');
	$post_order_array = array('a'=>'ASC', 'd'=>'DESC');

	$faction = $post_faction_array[$_POST['faction']];
	if(!isset($faction)) $faction = '1 = 2';
	$faction = "WHERE ".$faction;
	
	$sort_by = $post_sort_by_array[$_POST['sort_by']];
	if(!isset($sort_by)) $sort_by = 'lname';
	$order = $post_order_array[$_POST['order']];
	if(!isset($order)) $order = 'DESC';

	$show_pics = $_POST['show_pics'];
	$show_kills = $_POST['show_kills'];
	$show_killed = $_POST['show_killed'];
	$show_feed = $_POST['show_feed']; 
	$show_starved = $_POST['show_starved'];
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
} else {
	$faction = '';
	$sort_by = 'lname';
	$order = 'ASC'; 
	$show_pics = 1;
	$show_kills = 1; 
	$show_killed = 0;
	$show_feed = 1; 
	$show_starved = 0;
	$page = 1;
}
?>

<?php include('template_top.php'); ?>

<?php print $message; ?>

<h3>Player List</h3>
<form name="playerListForm" action="players.php" method="post">
<input type="hidden" name="mode" value="refresh" />
<center>
<?php

$faction_array = array('a'=>'All', 'r'=>'Resistance', 'h'=>'Horde', 'd'=>'Deceased');
$sort_by_array = array('ln'=>'Last Name', 'fn'=>'First Name', 'ks'=>'Kills', 'kd'=>'Time Killed', 'fd'=>'Last Feeding', 'sd'=>'Time Starved');
$order_array = array('a'=>'Ascending', 'd'=>'Descending');
print "<select name='faction'>\n";
while(list($k,$v) = each($faction_array)) {
	print "<option value='$k'";
	if($_POST['faction'] == $k) print "selected";
	print ">$v</option>\n";
}
print "</select>\n\n<select name='sort_by'>\n";
while(list($k,$v) = each($sort_by_array)) {
	print "<option value='$k'";
	if($_POST['sort_by'] == $k) print " selected";
	print ">$v</option>\n";
}
print "</select>\n\n<select name='order'>\n";
while(list($k,$v) = each($order_array)) {
	print "<option value='$k'";
	if($_POST['order'] == $k) print "selected";
	print ">$v</option>\n";
}
print "</select>";

?>

<input type="hidden" name="faction_orig" value="<?php print (isset($_POST['faction'])) ? $_POST['faction'] : 'a'; ?>" />
<input type="hidden" name="sort_by_orig" value="<?php print (isset($_POST['sort_by'])) ? $_POST['sort_by'] : 'ln'; ?>" />
<input type="hidden" name="order_orig" value="<?php print (isset($_POST['order'])) ? $_POST['order'] : 'a'; ?>" />

<input type="button" onclick="goToCheck(<?php print $page; ?>);" value="Refresh"><br>
<input type='checkbox' name='show_pics' value='1' <?php if($show_pics) print "checked"; ?>> Pictures
<input type='checkbox' name='show_kills' value='1' <?php if($show_kills) print "checked"; ?>> Kills
<input type='checkbox' name='show_killed' value='1' <?php if($show_killed) print "checked"; ?>> Time Killed
<input type='checkbox' name='show_feed' value='1' <?php if($show_feed) print "checked"; ?>> Last Fed
<input type='checkbox' name='show_starved' value='1' <?php if($show_starved) print "checked"; ?>> Time Starved
</center>

<br /><br />

<?php
// prepare the pagination values
$records = $config['page_records'];
$start = ($page - 1) * $records;
$limit = " LIMIT ".$start.", ".$records;

// player query
$player_query = "SELECT * FROM $table_u $faction ORDER BY $sort_by $order";
$ret = mysql_query($player_query.$limit);

// count query
$count_query = "SELECT COUNT(user_id) AS count FROM $table_u $faction ORDER BY $sort_by $order";
$count_ret = mysql_query($count_query);
$count = mysql_fetch_assoc($count_ret);
$count = $count['count'];

// get the total page count
$pages = ceil($count / $config['page_records']);
?>
<input type="hidden" name="page" value="<?php print $page; ?>" />

<? if($count_ret) include('pagination.php'); ?>

<table width="100%" cellspacing="0" class="data-table">
<tr>
<?php
if($show_pics) print "<th>picture</th>";
?>
<th>name</th>
<th>affiliation</th>
<?php
if($show_kills) print "<th>kills</th>";
if($show_killed) print "<th>time of death</th>";
if($show_feed) print "<th>last feeding time</th>";
if($show_starved) print "<th>time of starvation</th>";
if($admin) print "<th></th>";
?>
</tr>

<?php
// display the records
if($ret && ($rows = mysql_num_rows($ret)) > 0)
{
	for($i = 0; $i < $rows; $i++) {
		$row_id = ($i % 2) + 1;
		print '<tr class="row'.$row_id.'">';
		
		$row = mysql_fetch_assoc($ret);
		
		if($show_pics) {
			print '<td align="center">';
			if(strlen($row['pic_path']) > 0) {
				print '<img src="'.$row['pic_path'].'" class="player-pic '.$state_translate[$row['state']].'-pic">';
			} else {
				print "no image<br>available";
			}
			print "</td>";
		}
		
		print '<td align="center">'.$row['fname'].' '.$row['lname'].'</td>';
		
		print '<td align="center">';
		if($row['state'] == -2 && !$reveal_oz) {
			print '<span class="resistance">resistance</span>';
		} else {
			print '<span class="'.$state_translate[$row['state']].'">'.$state_translate[$row['state']].'</span>';
		}
		print "</td>";
		
		if($show_kills) {
			print '<td align="center">';
			if($row['state'] == -2 && !$reveal_oz) {
				print "0";
			} else {
				print $row['kills'];
			}
			print "</td>";
		}
		
		if($show_killed) {
			print '<td align="center">';
			if($row['state'] <= 0 && ($row['state'] != -2 || $reveal_oz)) {
				print $row['killed'];
			}
			print "</td>";
		}
		
		if($show_feed) {
			print '<td align="center">';
			if($row['state'] <= 0 && ($row['state'] != -2 || $reveal_oz)) { 
				print $row['feed'];
			}
			print "</td>";
		}
		
		if($show_starved) {
			print '<td align="center">';
			if($row['state'] == 0) { 
				print $row['starved'];
			}
			print "</td>";
		}
		print "</tr>";
	}
} else {
	print '<tr><td colspan="7" align="center"> There are no records to display</td></tr>';
}
?>
</table>

<? if($count_ret) include('pagination.php'); ?>

</form>

<?php include('template_bottom.php'); ?>