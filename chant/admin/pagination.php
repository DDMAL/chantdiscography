<?php
session_start();
if($_SESSION['login']!=true || (trim($_SESSION['login'])=='')) {
	header("location: index.php");
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery_pagination.js"></script>


<title>Chant Discography - Admin</title>

<link rel="stylesheet" type="text/css" media="all" href="css/pagnation.css" />
<link rel="stylesheet" type="text/css" media="all" href="css/form.css" />

<script language="javascript">
 	function deleteRecord(rec_id, rectitle){
		if(confirm("Are you sure you want to delete this record titled " + rectitle + "?")){
 			$.post("implement/i.deleterecord.php", { id: rec_id}, //this is JSON that gets posted
 		 	function(data){

				var pageNum = 1;

				$("#content").load("pagination_data.php?page=" + pageNum);

            });
		} 
	}
	
	function dupRecord(rec_id){
 			$.post("implement/i.duplicateRecord.php", { id: rec_id}, //this is JSON that gets posted
 		 	function(data){
				$("#loading").html(data).slideDown("slow");
				var pageNum = 1;
				$("#content").load("pagination_data.php?page=" + pageNum);

            });
		
	}
 </script>
</head>

<body>
<?php
include_once('inc/nav.php');
?>
<?php
include('connections/conn.php');
mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");
$per_page = 50;

if(isset($_POST['searchtxt'])){
//echo $_POST['searchtxt'];
	$txt = $_POST['searchtxt'];
	$sql = "select *, MATCH(record_title, issue_number, performers, director, solo) AGAINST ('$txt') as score from record where MATCH(record_title, issue_number, performers, director, solo) AGAINST ('$txt') ORDER BY score DESC";
}else{
	$sql = "select * from record";
	
}
//Calculating no of pages
$sqlchantcount = "select count(*) as c from chant";
$result_ct = mysql_query($sqlchantcount);
$row_ct = mysql_fetch_array($result_ct);
$result = mysql_query($sql);
$count = mysql_num_rows($result);
$pages = ceil($count/$per_page)
?>
<div id="stylized" class="myform">

<form method="post" style="margin:5px; margin-top:5px;">
Search: <input name="searchtxt" type="text" size="50" maxlength="150" <?php if(isset($_POST['searchtxt'])){echo 'value="'.$_POST['searchtxt'].'"';} ?> id="searchtxt"/>
<input name="searchbut" value="Search" type="submit" />
</form>

Total Number of Records: <?php echo $count; ?></br>
Total Number of Chants: <?php echo $row_ct['c']; ?>

<div id="loading" ></div>
<div id="content" ></div>
<ul id="pagination">
<?php
//Pagination Numbers
for($i=1; $i<=$pages; $i++)
{
echo '<li id="'.$i.'">'.$i.'</li>';
}
?>
</ul>
</div>
</body>

</html>
