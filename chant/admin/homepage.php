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
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas({fullPanel : true}) });
</script>
<title>Chant Discography - Admin</title>

 <link rel="stylesheet" type="text/css" media="all" href="css/form.css" />
 
 

</head>

<body>
<?php
$path = "/library/webserver/ftl/chant/";

//write data
if(isset($_POST['home'])){
	$fp = fopen($path."home.txt", 'w');
	fwrite($fp, $_POST['home']);
	fclose($fp);
	$fp = fopen($path."search.txt", 'w');
	fwrite($fp, $_POST['search']);
	fclose($fp);
	$fp = fopen($path."abbreviations.txt", 'w');
	fwrite($fp, $_POST['abbreviations']);
	fclose($fp);
	$fp = fopen($path."tropes.txt", 'w');
	fwrite($fp, $_POST['tropes']);
	fclose($fp);
	$fp = fopen($path."background.txt", 'w');
	fwrite($fp, $_POST['background']);
	fclose($fp);
	$fp = fopen($path."print.txt", 'w');
	fwrite($fp, $_POST['print']);
	fclose($fp);
	$fp = fopen($path."liber.txt", 'w');
	fwrite($fp, $_POST['liber']);
	fclose($fp);
	$fp = fopen($path."records.txt", 'w');
	fwrite($fp, $_POST['records']);
	fclose($fp);
}
//

include_once('inc/nav.php');
?>


<h1>Chant Discography - Update Home Page</h1>
<p>This is where you edit content on Home Page.</p>

<form action="homepage.php" method="post">
<strong>Home:</strong>
<textarea name="home" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."home.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Searching</strong>
<textarea name="search" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."search.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Abbreviations</strong>
<textarea name="abbreviations" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."abbreviations.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Tropes</strong>
<textarea name="tropes" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."tropes.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Background</strong>
<textarea name="background" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."background.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Print edition</strong>
<textarea name="print" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."print.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Liber Usualis</strong>
<textarea name="liber" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."liber.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <i> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <sup> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<strong>Records Needed</strong>
<textarea name="records" cols="35" style="width: 800px; background-color:#FFF">
 <?php 
$fp = fopen($path."records.txt", 'r');
while (!feof($fp))
{ 
 $text = fgetss($fp, 100000, "<a> <b> <img> <font> <br> <p> <strong> <hr> <center> <div> <address> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <span> <sub> <ol> <li> <i>");
echo stripslashes($text);
}

fclose($fp);
 ?>
</textarea>
<input name="submit" value="Submit" type="submit" />

</form>
</body>

</html>
