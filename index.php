<?php include('core.php');

$wowArmory = new WowArmory();
$realm = 'Blackrock';
$name = 'Jackdd';

$renderData = $wowArmory->getRenderData($realm , $name);

?>

  <object width="100%" height="300px" type="application/x-shockwave-flash"
                        data="http://wow.zamimg.com/modelviewer/ZAMviewerfp11.swf" width="100%" height="100%"
                        id="paperdoll-model-paperdoll-0-equipment-set">
                    <param name="quality" value="high">
                    <param name="wmode" value="direct"/>
                    <param name="allowsscriptaccess" value="always">
                    <param name="menu" value="false">
                    <param name="flashvars"
                           value="model=<?= $renderData['racegender']; ?>&amp;modelType=16&amp;contentPath=http://wow.zamimg.com/modelviewer/&amp;equipList=<?= $renderData['appearance']; ?>"/>
                </object>

