<?php
header("Content-Type: application/csv");
header('Content-Disposition: attachment; filename="chant.csv"');

include('connections/conn.php');
mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");

//get contents

$sql = "select * from record order by serial_num";
$result = mysql_query($sql) or die("Opps some thing went wrong");
echo "Format,Label Name,Record Title,Serial Num \n";

		//Print the contents
		$i=1;
		while($row = mysql_fetch_array($result))
		{

			$record_title=$row['record_title'];
			$label_name=$row['label_name'];
			$record_id=$row['id'];
			
			echo $row['format_code'].",".$label_name.",".$record_title.",".$row['serial_num']."\n";
			$i=$i+1;
		} //while
?>


