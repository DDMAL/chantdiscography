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
<script type="text/javascript" src="js/jgo.js"></script>

<title>Chant Discography - Admin</title>

 <link rel="stylesheet" type="text/css" media="all" href="css/form.css" />
 
 <?php
 // Update a Record
 if (isset($_GET['id'])){
 ?>
 <script language="javascript">
 	var rec_id= <?php echo $_GET['id']?>;
 	$.post("implement/i.record.php", { update: rec_id}, //this is JSON that gets posted
 		 function(data){

               $("#loadName").html(data).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
			   $("button.sendName").val('update');
                $("button.sendName").html('Update Chant');

    }); 
	
	function deleteTrack(chant_id, chanttitle){
		if(confirm("Are you sure you want to delete this chant titled " + chanttitle + "?")){
 			$.post("implement/i.deleteTrack.php", { id: chant_id}, //this is JSON that gets posted
 		 	function(data){

				$.post("implement/i.record.php", { update: rec_id}, //this is JSON that gets posted
 		 		function(data){
					str = '<font color="red"> Chant '+ chanttitle  +' was deleted.</font>';
               		$("#loadName").html(data).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
					$("#feedback").html(str).slideDown("slow"); // send this data from the PHP echo into the #screen DIV
			   		$("button.sendName").val('update');
                	$("button.sendName").html('Update Chant');

    			}); 

            });
		} 
	
	}
 </script>
 <?php	
 }
// END update a record
 ?>

</head>

<body>
<?php
include_once('inc/nav.php');
?>

<div id="stylized" class="myform">

<h1>Chant Discography - Insert</h1>
<p>This is where you enter chants into database.</p>
<div id="feedback"></div>
<div id="loadName">

<label>Serial Number:
</label>
<input name="serial_num" type="text" id="serial_num" tabindex="1" size="9" maxlength="9" />
<br />
<label>Format Code:
</label>
<input name="format_code" type="text" id="format_code" tabindex="2" size="2" maxlength="2" />
<br />
<label>Country Code:</label>
 <input id="country_code" type="text" tabindex="3" size="2" maxlength="2" />
<br />

<label>Label Name:</label>
<input id="label_name" type="text" tabindex="4" size="35" maxlength="55" />
<br />
<label>Prefix to Number:</label>
<input id="prefix_to_num" type="text" tabindex="5" size="15" maxlength="15" />
<br />
<label>Issue Number:</label>
<input id="issue_num" type="text" tabindex="6" size="15" maxlength="15" />
<br />
<label>Suffix:</label>
<input id="suffix" type="text" tabindex="7" size="15" maxlength="15" />
<br />
<label>Record Title:</label>
<input id="record_title" type="text" tabindex="8" size="35" maxlength="75" />
<br />
<label>Alternate Numbers:</label>
<input id="alt_num" type="text" tabindex="9" size="35" maxlength="255" />
<br />
<label>Performers:</label>
<input id="performers" type="text" tabindex="10" size="35" maxlength="75" />
<br />
<label>Director:</label>
<input id="director" type="text" tabindex="11" size="35" maxlength="75" />
<br />
<label>Solo:</label>
<input id="solo" type="text" tabindex="12" size="35" maxlength="75" />
<br />
<label>Date:</label>
<input id="date" type="text" tabindex="13" size="35" maxlength="50" />
<br />
<label>Keywords:</label>
<input id="keywords" type="text" tabindex="14" size="55" maxlength="250" />
<br />
<label>Comments:</label>
<textarea cols="35" rows="5" id="comments" tabindex="15"></textarea>

</div>

<button type="button" class="sendName" value="record">Submit Record</button>

<div id="results"></div>

<div class="spacer"></div>


</div>
</div>
</body>

</html>
