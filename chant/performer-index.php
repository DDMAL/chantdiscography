<?php echo '<?xml version="1.0" encoding="ISO-8859-1"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
<title>Chant Discography</title>
</head>

<body >
<div style="width:800px;margin:0 auto; text-align:right; font-size:12pt">
	<a href="?contact" style="color:#666;text-decoration:none;">Contact Us</a><br />
    <a href="?links" style="color:#666;text-decoration:none;">Links</a>
</div>
<div id="header">
  <div style=" position:absolute; margin-top:75px; margin-left:220px;"><form method="get" action="index.php"><input name="searchtxt" type="text" class="searchfield" maxlength="150" /> 
      <input name="Go" type="submit" value="SEARCH" />
	</form>
<span style="margin-left:115px"><a href="chant-index.php">Chants</a> | <a href="record-index.php">Records</a> | <a href="performer-index.php">Performers</a></span>
    </div>
</div>

<ul>
    	<li><a href="index.php?home">Home</a></li>
        <li><a href="index.php?search">Searching</a></li>
    	<li><a href="index.php?abbreviations">Abbreviations</a></li> 
        <li><a href="index.php?tropes">Tropes</a></li>
        <li><a href="index.php?background">Background</a></li> 
        <li><a href="index.php?print">Print edition</a></li> 
        <li><a href="index.php?liber">Liber Usualis</a></li>
        <li><a href="index.php?records">Records Needed</a></li>
      </ul>


<div id="results">

    <?php
	
	include('admin/connections/conn.php');
	mysql_select_db($database_ndb, $ndb) or die("Opps some thing went wrong");
	$sql = "select DISTINCT performers from record  ORDER BY performers ";
	
	$result = mysql_query($sql) or die("Opps some thing went wrong");
	echo "<h3>Performers:</h3>";
	while($row = mysql_fetch_array($result)){
		echo "<span id='title'><a href='index.php?type=performer&searchtxt=".$row['performers']."'>".$row['performers']."</a></span><br>";
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
<a href="index.php?home">Home</a> | <a href="index.php?search">Searching</a> | <a href="index.php?abbreviations">Abbreviations</a> | <a href="index.php?tropes">Tropes</a> | <a href="index.php?background">Background</a> | <a href="index.php?print">Print edition</a> | <a href="index.php?liber">Liber Usualis</a> | <a href="index.php?records">Records Needed</a> | <a href="index.php?contact">Contact Us</a>  | <a href="index.php?links">Links</a>
 
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
