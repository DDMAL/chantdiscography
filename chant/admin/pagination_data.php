<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include('connections/conn.php');
mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");

$per_page = 50; 

if($_GET)
{
$page=$_GET['page'];
}



//get table contents
$start = ($page-1)*50;
if(isset($_GET['strtxt'])){
	$txt = $_GET['strtxt'];// echo $txt;
	$sql = "select *, MATCH(record_title, issue_number, performers, director, solo) AGAINST ('$txt') as score from record where MATCH(record_title, issue_number, performers, director, solo) AGAINST ('$txt') ORDER BY score DESC";
//	echo $sql;
}else{
	$sql = "select * from record order by serial_num limit $start,$per_page";
}
//$sql = "select * from record";
//echo $sql;
$result = mysql_query($sql) or die("Opps some thing went wrong");
//echo $result;
?>
<h2>Record List</h2>

	<table width="100%">
		<tr><th>Format</th><th>Label Name</th><th>Record Title</th><th>Serial Num</th><th></th></tr>
		<?php
		//Print the contents
		$i=1;
		while($row = mysql_fetch_array($result))
		{

			$record_title=$row['record_title'];
			$label_name=$row['label_name'];
			$record_id=$row['id'];
			
			if($i % 2) { 
		  		echo '<tr bgcolor="lightgray">';
			}else { 
				echo '<tr bgcolor="white">';
			}
		?>
		
			<td width="20"><?php echo $row['format_code']; ?></td>
			<td><?php echo $label_name; ?></td>
			<td><?php echo $record_title; ?></td>
            <td width="100px"><?php echo $row['serial_num']; ?></td>
			<td width="70px" ><a href="insert.php?id=<?php echo $record_id;?>">edit</a> <a href="#" onclick="deleteRecord('<?php echo $record_id."', '".$record_title;?>');">delete</a> <!-- <a href="#" onclick="dupRecord('<?php echo $record_id;?>');">duplicate</a> --></td>
		</tr>
		<?php
			$i=$i+1;
		} //while
		?>
	</table>

