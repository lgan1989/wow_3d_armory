<?php include('core.php');
$wowArmory = new WowArmory();
$realm = 'booty-bay';
$name = 'Мансе';
$renderData = $wowArmory->getRenderData($realm , $name);
?>
<!DOCTYPE html>

<head>
	<title>WoW Model Viewer</title>
	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<style>body {background: #191919;}</style>
	<script>
		$(document).ready(function() { var is_hd = true;

		$('.hdmodel').change(function() { is_hd = $(this).val(); change_quality(); });
			function change_quality() {
			var obj = $('object:first');
			var container = $(obj).parent();
			var flashvars = $('param[name=\'flashvars\']').attr('value');
				if(is_hd == 'true') {
				flashvars = flashvars.replace('hd=false', 'hd=true');
				} else {
				flashvars = flashvars.replace('hd=true', 'hd=false');}

		$('param[name=\'flashvars\']').attr('value', flashvars);
			var newobj = $(obj).clone();
		$(obj).remove();
		$(container).append( newobj );} });
	</script>
</head>

<body>
<div class="paperdoll-model" style="height: 600px; width: 100%;">
	<object width="100%" height="100%" type="application/x-shockwave-flash" data="http://wow.zamimg.com/modelviewer/ZAMviewerfp11.swf" width="100%" height="100%" id="paperdoll-model-paperdoll-0-equipment-set">
		<param name="quality" value="high">
		<param name="wmode" value="direct"/>
		<param name="allowsscriptaccess" value="always">
		<param name="allowfullscreen" value="true">
		<param name="menu" value="false">
		<param name="flashvars" value="model=<?= $renderData['racegender']; ?>&amp;modelType=16&amp;cls=11&amp;equipList=<?= $renderData['appearance']; ?>&amp;contentPath=//wow.zamimg.com/modelviewer/&amp;container=paperdoll-model-paperdoll-0-equipment-set&amp;hd=true&amp"/>
	</object>
</div>
	<select class="hdmodel">
		<option value="true">Warlords of Draenor</option>
		<option value="false">World of Warcraft</option>
	</select>
</body>
