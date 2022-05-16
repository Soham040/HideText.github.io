<?php



$public = isset($_POST['public']) ? $_POST['public'] : null;
$private = isset($_POST['private']) ? $_POST['private'] : null;
$encoded = isset($_POST['encoded']) ? $_POST['encoded'] : null;


function str2bin($text){
	$bin = array();
	for($i=0; strlen($text)>$i; $i++)
		$bin[] = decbin(ord($text[$i]));
	return implode(' ', $bin);
}

function wrap($string) {
	return "\xEF\xBB\xBF".$string."\xEF\xBB\xBF";
}


function unwrap($string) {
	$tmp = explode("\xEF\xBB\xBF", $string);
	if(count($tmp) == 1) return false; 
	return $tmp[1];
}


function bin2str($bin){
	$text = array();
	$bin = explode(' ', $bin);
	for($i=0; count($bin)>$i; $i++)
		$text[] = chr(@bindec($bin[$i]));
	return implode($text);
}


function bin2hidden($str) {
	$str = str_replace(' ', "\xE2\x81\xA0", $str); 
	$str = str_replace('0', "\xE2\x80\x8B", $str); 
	$str = str_replace('1', "\xE2\x80\x8C", $str); 
	return $str;
}


function hidden2bin($str) {
	$str = str_replace("\xE2\x81\xA0", ' ', $str);
	$str = str_replace("\xE2\x80\x8B", '0', $str); 
	$str = str_replace("\xE2\x80\x8C", '1', $str); 
	return $str;
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">

<meta property="og:description" content="Hide text in plain sight using invisible zero-width characters.">
<title>HideText</title>
<style>
@import url('https://rsms.me/inter/inter.css');
html { font-family: 'Inter', sans-serif; }
@supports (font-variation-settings: normal) {
	html { font-family: 'Inter var', sans-serif; }
}
body {
	margin: 2em 4em;
}
label {
	font-weight: bold;
	display: block;
}
form {
	margin: 2em 0;
}
fieldset {
	border: 1px solid #777;
	padding: 1em 2em;
	border-radius: 0.2em;
}
legend {
	font-size: 150%;
	font-weight: bold;
	padding: 0 .5em;
}
textarea {
	width: 100%;
	height: 4.4em;
	margin-bottom: 1em;
}
</style>
</head>
<body  text = "#FFFFFF" style= "background-color: black;" >>

<main>

<h1>HideMessageinText</h1>


<p>Enter a public message, then a private message, and then click the button to hide your private message within your public message. If you’ve received a public message, you can reveal the private message here as well.</p>

<section>

<div style="display: grid; grid-auto-rows: 1fr; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); grid-gap: 2em;">

<form action="?" method="post">
<fieldset>
<legend>Hide</legend>
<div class="group">
<label for="public">Public message</label>
<textarea name="public"><?php echo $public; ?></textarea>
</div>
<div class="group">
<label for="private">Private message</label>
<textarea name="private"><?php echo $private; ?></textarea>
</div>
<p><button type="submit"><i class="fas fa-pencil-alt"></i> Steganographize</button></p>
</fieldset>
</form>

<form action="?" method="post">
<fieldset>
<legend>Reveal</legend>
<div class="group">
<label for="encoded">Public message</label>
<textarea name="encoded" style="height: 11.5em;"><?php echo $encoded; ?></textarea>
</div>
<p><button type="submit"><i class="fas fa-eye"></i> Desteganographize</button></p>
</form>
</div>

</div>

</section>

<?php

if(isset($_POST['public'])) {
	echo '<section class="notice"><h2>Steganographized Message</h2>';
	
	$public = $_POST['public'];
	$public = mb_str_split($public);
	
	$half = round(count($public) / 2);
	
	$private = $_POST['private'];
	
	$private = str2bin($private);
	
	$private = bin2hidden($private);
	
	$private = wrap($private);
	
	$i = 0;
	$tmp = array();
	if(count($public) == 1) {
		$tmp[0] = $public[0];
		$tmp[1] = $private;
	}
	else {
		foreach($public as $char) {
			if($i == $half) {
				$tmp[] = $private;
			}
			$tmp[] = $char;
			$i++;
		}
	}
	
	
	$public = implode('', $tmp);
	
	
	echo '<textarea style="height: 5em;">'.$public.'</textarea>';
	echo '<p>Copy the text above, and your private message will come along for the ride.</p>';    
	echo '</div>';
}

if(isset($_POST['encoded'])) {
	
	
	$unwrapped = unwrap($_POST['encoded']);
	
	
	if(!$unwrapped) {
		$message = bin2str(hidden2bin($_POST['encoded']));
	}
	
	else {
		$message = bin2str(hidden2bin($unwrapped));
	}
	  
	// Display the hidden private message
	echo '<section class="notice"><h2>Private Message</h2>';
	if(strlen($message) < 2) {
		echo '<p class="alert"><i class="fas fa-exclamation-triangle"></i> No private message was found.</p>';
	}
	else {
		echo '<p style="font-weight: bold;">'.htmlentities($message).'</p>';
	}
}

?>

</section>

<br>

<section id="about" class="attention">

<p><b>~Made by Soham</b></p>

</section>

</main>

</body>
</html>