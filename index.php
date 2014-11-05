<?php include('core.php');

$wowArmory = new WowArmory();
$realm = 'booty-bay';
$name = 'Мансе';
$server = 'eu';
$renderData = $wowArmory->getRenderData($realm , $name);

?>
<!DOCTYPE html>

<head>
	<title>WoW Model Viewer</title>
	
	<style>body {background: black;}</style>
</head>

<body>
	<object width="100%" height="600px" type="application/x-shockwave-flash" data="http://wow.zamimg.com/modelviewer/ZAMviewerfp11.swf" width="100%" height="100%" id="paperdoll-model-paperdoll-0-equipment-set">
		<param name="quality" value="high">
		<param name="wmode" value="direct"/>
		<param name="allowsscriptaccess" value="always">
		<param name="menu" value="false">
		<param name="flashvars" value="model=<?= $renderData['racegender']; ?>&amp;modelType=16&amp;contentPath=http://wow.zamimg.com/modelviewer/&amp;equipList=<?= $renderData['appearance']; ?>"/>

	</object>
	
	<select class="animation" style="display: inline;">
		<option value="Attack1H">Attack1H</option>
		<option value="Attack2H">Attack2H</option>
		<option value="Meditate">Meditate</option>
		<option value="Stop">Stop</option>
	</select>
	
	<select class="hdmodel">
		<option value="true">Warlords</option>
		<option value="false">World of Warcraft</option>
	</select>
	
</body>
