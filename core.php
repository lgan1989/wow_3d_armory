<?php 
class WowArmory 
{

    private $region = 'us';

    public function __construct($region = 'us')
    {
        $this->region = $region;
    }

    public $wow_race = array(
        1 => 'human',
        2 => 'orc',
        3 => 'dwarf',
        4 => 'nightelf',
        5 => 'scourge',
        6 => 'tauren',
        7 => 'gnome',
        8 => 'troll',
        9 => 'goblin',
        10 => 'bloodelf',
        11 => 'draenei',
        22 => 'worgen',
        25 => 'pandaren'

    );
    
    public $wow_gender = array(
        0 => 'male',
        1 => 'female'
    );
    public function getDisplayId($item)
    {
        if ($item === null || !is_object($item)) {
            return '';
        }

        $tooltipParams = isset($item->tooltipParams) ? $item->tooltipParams : null;
        if (is_object($tooltipParams) && property_exists($tooltipParams, 'transmogItem') && !empty($tooltipParams->transmogItem)) {
            $id = $tooltipParams->transmogItem;
        } elseif (isset($item->id)) {
            $id = $item->id;
        } else {
            return '';
        }

        $url = 'http://www.wowhead.com/item=' . $id . '&xml';
        $ch = curl_init($url);
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.66 Safari/537.36');

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return '';
        }

        $p = xml_parser_create();
        $vals = array();
        $index = array();
        if (!xml_parse_into_struct($p, $response, $vals, $index)) {
            xml_parser_free($p);
            return '';
        }
        xml_parser_free($p);

        $jsonIndex = isset($index['JSON'][0]) ? $index['JSON'][0] : null;
        if ($jsonIndex === null || !isset($vals[$jsonIndex]['value'])) {
            return '';
        }

        $slotPos = strpos($vals[$jsonIndex]['value'], 'slotbak":');
        if ($slotPos === false) {
            return '';
        }

        $slotInfo = strstr(substr($vals[$jsonIndex]['value'], $slotPos), ',', true);
        if ($slotInfo === false) {
            return '';
        }

        $position = substr(strstr($slotInfo, ':'), 1);
        $position = trim($position);

        $iconIndex = isset($index['ICON'][0]) ? $index['ICON'][0] : null;
        if ($iconIndex === null || !isset($vals[$iconIndex]['attributes']['DISPLAYID'])) {
            return $position;
        }

        return $position . ',' . $vals[$iconIndex]['attributes']['DISPLAYID'];
    }

    public function getRenderData($realm = '', $charater = '')
    {
        if (empty($realm) || empty($charater)) {
            return array(
                'thumbnail' => '',
                'face' => '',
                'skin' => '',
                'hairv' => '',
                'hairc' => '',
                'feature' => '',
                'appearance' => '',
                'racegender' => ''
            );
        }

        $url = 'http://' . $this->region . '.battle.net/api/wow/character/' . $realm . '/' . $charater . '?fields=appearance,items';
        $ch = curl_init($url);

        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.66 Safari/537.36');

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return array(
                'thumbnail' => '',
                'face' => '',
                'skin' => '',
                'hairv' => '',
                'hairc' => '',
                'feature' => '',
                'appearance' => '',
                'racegender' => ''
            );
        }

        $data = json_decode($response);
        if (!is_object($data)) {
            return array(
                'thumbnail' => '',
                'face' => '',
                'skin' => '',
                'hairv' => '',
                'hairc' => '',
                'feature' => '',
                'appearance' => '',
                'racegender' => ''
            );
        }

        $items = isset($data->items) && is_object($data->items) ? $data->items : new stdClass();
        $appearance = isset($data->appearance) && is_object($data->appearance) ? $data->appearance : new stdClass();

        $race = isset($data->race) ? (int)$data->race : null;
        $gender = isset($data->gender) ? (int)$data->gender : null;

        $render = array(
            'head' => '',
            'shoulder' => '',
            'back' => '',
            'chest' => '',
            'shirt' => '',
            'tabard' => '',
            'wrist' => '',
            'hands' => '',
            'waist' => '',
            'legs' => '',
            'feet' => '',
            'mainHand' => '',
            'offHand' => ''
        );

        if (isset($items->head) && !empty($appearance->showHelm)) {
            $render['head'] = $this->getDisplayId($items->head);
        }
        if (isset($items->shoulder)) {
            $render['shoulder'] = $this->getDisplayId($items->shoulder);
        }
        if (isset($items->back) && !empty($appearance->showCloak)) {
            $render['back'] = $this->getDisplayId($items->back);
        }
        if (isset($items->chest)) {
            $render['chest'] = $this->getDisplayId($items->chest);
        }
        if (isset($items->shirt)) {
            $render['shirt'] = $this->getDisplayId($items->shirt);
        }
        if (isset($items->tabard)) {
            $render['tabard'] = $this->getDisplayId($items->tabard);
        }
        if (isset($items->wrist)) {
            $render['wrist'] = $this->getDisplayId($items->wrist);
        }
        if (isset($items->hands)) {
            $render['hands'] = $this->getDisplayId($items->hands);
        }
        if (isset($items->waist)) {
            $render['waist'] = $this->getDisplayId($items->waist);
        }
        if (isset($items->legs)) {
            $render['legs'] = $this->getDisplayId($items->legs);
        }
        if (isset($items->feet)) {
            $render['feet'] = $this->getDisplayId($items->feet);
        }
        if (isset($items->mainHand)) {
            $render['mainHand'] = $this->getDisplayId($items->mainHand);
        }
        if (isset($items->offHand)) {
            $render['offHand'] = $this->getDisplayId($items->offHand);
        }

        $renderString = '';
        foreach ($render as $renderItem) {
            if ($renderItem) {
                $renderString .= ',' . $renderItem;
            }
        }

        $renderData = array();
        $renderData['thumbnail'] = isset($data->thumbnail) ? $data->thumbnail : '';
        $renderData['face'] = isset($appearance->faceVariation) ? $appearance->faceVariation : '';
        $renderData['skin'] = isset($appearance->skinColor) ? $appearance->skinColor : '';
        $renderData['hairv'] = isset($appearance->hairVariation) ? $appearance->hairVariation : '';
        $renderData['hairc'] = isset($appearance->hairColor) ? $appearance->hairColor : '';
        $renderData['feature'] = isset($appearance->featureVariation) ? $appearance->featureVariation : '';

        $renderData['appearance'] = substr($renderString, 1, strlen($renderString) - 1) .
            '&sk=' . $renderData['skin'] .
            '&ha=' . $renderData['hairv'] .
            '&hc=' . $renderData['hairc'] .
            '&fa=' . $renderData['face'] .
            '&fh=' . $renderData['feature'] .
            '&fc=' . $renderData['feature'] .
            '&mode=3';

        $raceName = isset($this->wow_race[$race]) ? $this->wow_race[$race] : '';
        $genderName = isset($this->wow_gender[$gender]) ? $this->wow_gender[$gender] : '';
        $renderData['racegender'] = $raceName . $genderName;

        return $renderData;
    }

}
