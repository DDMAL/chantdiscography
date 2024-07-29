<?php echo '<?xml version="1.0" encoding="ISO-8859-1"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<?php
if(isset($_GET['searchtxt']) || isset($_GET['id'])){
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
}
?>
<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<title>Chant Discography</title>
</head>

<body >
<div style="width:800px;margin:0 auto; text-align:right; font-size:12pt">
	<a href="?contact" style="color:#666;text-decoration:none;">Contact Us</a><br />
    <a href="?links" style="color:#666;text-decoration:none;">Links</a>
</div>
<div id="header">
  <div style=" position:absolute; margin-top:75px; margin-left:220px;"><form method="get" ><input name="searchtxt" type="text" class="searchfield" maxlength="150" /> 
      <input name="Go" type="submit" value="SEARCH" />
	</form>
	<span style="margin-left:115px"><a href="chant-index.php">Chants</a> | <a href="record-index.php">Records</a> | <a href="performer-index.php">Performers</a></span>
    </div>
</div>

<ul>
    	<li><a href="?home">Home</a></li>
        <li><a href="?search">Searching</a></li>
    	<li><a href="?abbreviations">Abbreviations</a></li> 
        <li><a href="?tropes">Other Articles</a></li>
        <li><a href="?background">Background</a></li> 
        <li><a href="?print">Print edition</a></li> 
        <li><a href="?liber">Liber Usualis</a></li>
        <li><a href="?records">Records Needed</a></li>
      </ul>


<div id="results">

    <?php
	$path = "/library/webserver/ftl/chant/";
if(isset($_GET['searchtxt'])){
	$txt = $_GET['searchtxt'];
	include('admin/connections/conn.php');
	mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");
	if($_GET['type']!="chant"){
	if($_GET['type']=="performer"){
		$sql = "select * from record where performers LIKE '%".$txt."%'";
}else{
	$sql = "select *, MATCH(record_title, issue_number, performers, director, solo, keywords) AGAINST ('$txt' IN BOOLEAN MODE) as score from record where MATCH(record_title, issue_number, performers, director, solo, keywords) AGAINST ('$txt' IN BOOLEAN MODE) ORDER BY score DESC, serial_num ";
	}

	$result = mysql_query($sql) or die("Opps some thing went wrong");
	echo "<h3>Record:</h3>";
	while($row = mysql_fetch_array($result)){
		echo "<span id='title'><a href='?id=".$row['id']."'>".$row['record_title']."</a></span><br>
			<span id='details'>Format: ".$row['format_code'].
			"<br />Country Code: ".$row['country_code'].
			"<br />Label: ".$row['label_name']."</span>
			<br /><span id='date'>Date: ".$row['date_of_recording']."</span>
			<hr>";
	}
	}

if($_GET['type']=="chant"){
	$sql = "select * from chant where title_of_chant LIKE '%".$txt."%'";
}else{
	$sql = "select c.id, serial_num, title_of_chant, page, record_title, record_id, label_name, prefix_to_number, issue_number, suffix, performers, director, solo, time, c.comments, MATCH(title_of_chant, page) AGAINST ('$txt' IN BOOLEAN MODE) as score from chant c, record r where MATCH(title_of_chant, page) AGAINST ('$txt' IN BOOLEAN MODE)  AND record_id=r.id ORDER BY score DESC, serial_num";
}

	$result = mysql_query($sql) or die("Opps some thing went wrong");
	echo "<h3>Chant:</h3>";
	while($row = mysql_fetch_array($result)){
		echo  "<span id='title'>".$row['title_of_chant']."</span><span id='details'> ".$row['page'].", ".$row['label_name']." ".$row['prefix_to_number']." ".$row['issue_number']." ".$row['suffix']."<br />
			<span id='date'>";
			
			if($row['performers']){echo $row['performers'].", ";}
			if($row['director']){echo " ".$row['director'].", ";}
			if($row['solo']){echo " ".$row['solo'].", ";}
			
			
			echo " [".$row['time']."] - ".$row['comments']."<br /></span>
			Record Title: <a href='?id=".$row['record_id']."'>".$row['record_title']."</a>
			</span>
			<hr>";
	}
}

//Display Record Details.
elseif(isset($_GET['id'])){
	include('admin/connections/conn.php');
	mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");
	$sql = "select record_title, format_code, country_code, label_name, prefix_to_number, 		
			issue_number, suffix, alternate_num, performers, director, solo, date_of_recording, 
			comments
			from record where id=".mysql_real_escape_string($_GET['id']);
	$result = mysql_query($sql) or die("Opps some thing went wrong");
	$row = mysql_fetch_array($result);
	echo "<h3>".$row['record_title']."</h3>
			<p><span id='title'>".$row['format_code']."=".$row['country_code']."=".
			$row['label_name']." ".$row['prefix_to_number']." ".$row['issue_number']." ".$row['suffix']."<br>
			Title:</span> <span id='details'>".$row['record_title']."<br>
			<span id='title'>Also issued as:</span> ".$row['alternate_num']."<br>
			<span id='title'>Performers:</span> ".$row['performers']."<br>
			<span id='title'>Director:</span> ".$row['director']."<br>
			<span id='title'>Solo:</span> ".$row['solo']."<br>
			<span id='title'>Date:</span> <span id='date'>".$row['date_of_recording']."</span><br>
			<span id='title'>Comments:</span> ".$row['comments']."
			</span></p><hr>
			<span id='title'>Chant List:</span>";	
			
	$sql = "select item_num, track_num, title_of_chant, page, time, comments
			from chant where record_id=".mysql_real_escape_string($_GET['id'])." Order by item_num";
	$result = mysql_query($sql) or die("Opps some thing went wrong");
	echo "<p><span id='details'>";
	while($row = mysql_fetch_array($result)){
		echo $row['track_num'].". ".$row['title_of_chant']." ".$row['page']." [".$row['time']."] -- ".$row['comments']."<br>";	
	}
	echo "</span></p>";
}else {
	if(isset($_GET['search'])){?>
     <?php 
$fp = fopen($path."search.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
<?php
	}elseif(isset($_GET['abbreviations'])){?>
   <?php 
$fp = fopen($path."abbreviations.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
 
<?php
	}elseif(isset($_GET['tropes'])){?>
   <?php 
$fp = fopen($path."tropes.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
<?php
	}elseif(isset($_GET['background'])){?>
  <?php 
$fp = fopen($path."background.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
  
  <?php
	}elseif(isset($_GET['print'])){?>
 <?php 
$fp = fopen($path."print.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
  
  <?php	
	}else if(isset($_GET['liber'])){?>
 <?php 
$fp = fopen($path."liber.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <i> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <sup> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
<?php	
	}else if(isset($_GET['records'])){?>
 <?php 
$fp = fopen($path."records.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li>");
echo stripslashes($text);
}

fclose($fp);
 ?>
 <?php	
	}else if(isset($_GET['links'])){?>
     <?php 
$fp = fopen($path."links.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
   
     <?php	
	}else if(isset($_GET['contact'])){?>
    <strong>Contact Us:</strong>
    <style type="text/css">
form.cmxform label.error, label.error {
	/* remove the next line when you have trouble in IE6 with labels in list */

	color: red;

	font-style: italic

}
</style>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<!-- for styling the form -->
<script src="js/cmxforms.js" type="text/javascript"></script>
<script type="text/javascript">

$().ready(function() {
	// validate signup form on keyup and submit
	$("#commentForm").validate({
		rules: {
			name: "required",
			message: {
				required: true,
				minlength: 3,
				maxlength: 500
			},
			email: {
				required: true,
				email: true
			}
		},
		messages: {
			name: "Please enter your name",
			message: {
				required: "Please enter a message",
				minlength: "Enter at least 3 characters"
			},
			email: "Please enter a valid email address",
			
		}
	});
	
	
});
</script>
     <!--<form class="cmxform" id="commentForm" action="https://ssl39.chi.us.securedata.net/ftlinteractive.com/chant/contact.php" method="post" >-->
     <form class="cmxform" id="commentForm" action="https://sslwsh006.securedata.net/ftlinteractive.com/chant/contact.php" method="post" >
<table width="960" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td colspan="2">COMPLETE INFORMATION BELOW - *Required</td>
  </tr>
  <tr> 
     <td width="130" nowrap="nowrap" align="right"><label>Name*</label></td>
     <td><input type="text" id="name" size="30" name="name" class="required"/></td>
  </tr>
  <tr> 
     <td width="130" nowrap="nowrap" align="right"><label>Email*</label></td>
     <td><input type="text" id="email" size="30" name="email" class="required email"/></td>
  </tr>
  
  <tr> 
     <td width="130" valign="top" nowrap="nowrap" align="right"><label>Message*</label></td>
     <td><textarea rows="5" cols="40" id="message" name="message"></textarea>
	</td>
  </tr>
   <tr> 
     <td colspan="2" style="padding-left:300px"><input type="submit" value="Submit" id="submit"></td>
   </tr>
</table>
	</form>
    <?php	
	}else if(isset($_GET['contactsuccess'])){?>
    <strong>Contact Us:</strong>
    <p> Your message was sent.</p>
  <?php }else{?>
  <?php 
$fp = fopen($path."home.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
<?php }
}
?>
</div>
<div id="footer" align="center">
<strong>Support has come from the following sources:</strong>
<p>The Central Library Resources Council</p>
<p>The Dom Mocquereau Fund</p>

<p><img src="img/pmms_logo.jpg" width="50" height="40" align="middle" /><br />
The Plainsong and Medieval Music Society </p>
<p>The Association for Recorded Sound Collections</p>
</div>
<div id="footer" align="center">
<a href="?home">Home</a> | <a href="?search">Searching</a> | <a href="?abbreviations">Abbreviations</a> | <a href="?tropes">Tropes</a> | <a href="?background">Background</a> | <a href="?print">Print edition</a> | <a href="?liber">Liber Usualis</a> | <a href="?records">Records Needed</a> | <a href="?contact">Contact Us</a>  | <a href="?links">Links</a>
 
  <p>&copy;2010
  </p>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8052833-5']);
  _gaq.push(['_setDomainName', '.chantdiscography.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
