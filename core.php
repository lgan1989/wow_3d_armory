<?php 
class WowArmory 
{
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
    
    public $wow_host = "http://eu.battle.net/static-render/eu/";
    
    public $wow_gender = array(
        0 => 'male',
        1 => 'female'
    );
    public function getDisplayId($item)
    {
        if ($item == null)
            return;
        if (property_exists($item, 'tooltipParams') && property_exists($item->tooltipParams, 'transmogItem')) {
            $id = $item->tooltipParams->transmogItem;
        } else $id = $item->id;
        $url = 'http://www.wowhead.com/item=' . $id . '&xml';

        $ch = curl_init($url);
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.66 Safari/537.36');
        $response = curl_exec($ch);
        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $vals, $index);
        xml_parser_free($p);


        $position = strstr(strstr($vals[$index['JSON'][0]]['value'], 'slotbak":'), ',', true);
        $position = substr(strstr($position, ':'), 1);
        $position = trim($position);


        return $position . ',' . $vals[$index['ICON'][0]]['attributes']['DISPLAYID'];

    }

    public function getRenderData($realm = '', $charater = '')
    {
        $url = 'http://eu.battle.net/api/wow/character/' . $realm . '/' . $charater . '?fields=appearance,items';
        $ch = curl_init($url);

        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.66 Safari/537.36');

        $response = curl_exec($ch);
        $data = json_decode($response);

        $race = $data->race;
        $gender = $data->gender;


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
        if (property_exists($data->items, 'head') && $data->appearance->showHelm)
            $render['head'] = $this->getDisplayId($data->items->head); #голова
        if (property_exists($data->items, 'shoulder'))
            $render['shoulder'] = $this->getDisplayId($data->items->shoulder); #Плечи
        if (property_exists($data->items, 'back') && $data->appearance->showCloak)
            $render['back'] = $this->getDisplayId($data->items->back); #спина
        if (property_exists($data->items, 'chest'))
            $render['chest'] = $this->getDisplayId($data->items->chest); #грудь
        if (property_exists($data->items, 'shirt'))
            $render['shirt'] = $this->getDisplayId($data->items->shirt); #рубашка
        if (property_exists($data->items, 'tabard'))
            $render['tabard'] = $this->getDisplayId($data->items->tabard); #накидка
        if (property_exists($data->items, 'wrist'))
            $render['wrist'] = $this->getDisplayId($data->items->wrist); #брасы
        if (property_exists($data->items, 'hands'))
            $render['hands'] = $this->getDisplayId($data->items->hands); #руки
        if (property_exists($data->items, 'waist'))
            $render['waist'] = $this->getDisplayId($data->items->waist); #пояс
        if (property_exists($data->items, 'legs'))
            $render['legs'] = $this->getDisplayId($data->items->legs); #штаны
        if (property_exists($data->items, 'feet'))
            $render['feet'] = $this->getDisplayId($data->items->feet); #боты
		if (property_exists($data->items, 'mainHand'))
            $render['mainHand'] = $this->getDisplayId($data->items->mainHand); #пуха
        if (property_exists($data->items, 'offHand'))
            $render['offHand'] = $this->getDisplayId($data->items->offHand); #офф хэнд

        curl_close($ch);

        $renderString = '';
        foreach ($render as $renderItem) {
            if ($renderItem)
                $renderString .= ',' . $renderItem;
        }


        $renderData['thumbnail'] = $data->thumbnail;
        $renderData['face'] = $data->appearance->faceVariation;
        $renderData['skin'] = $data->appearance->skinColor;
        $renderData['hairv'] = $data->appearance->hairVariation;
        $renderData['hairc'] = $data->appearance->hairColor;
        $renderData['feature'] = $data->appearance->featureVariation;

        $renderData['appearance'] = substr($renderString, 1, strlen($renderString) - 1) .
        '&sk='.$renderData['skin'].
        '&ha='.$renderData['hairv'].
        '&hc='.$renderData['hairc'].
        '&fa='.$renderData['face'].
        '&fh='.$renderData['feature'].
        '&fc='.$renderData['feature'].
        '&mode=3';
        $renderData['racegender'] = $this->wow_race[$data->race] . $this->wow_gender[$data->gender];
        return $renderData;
    }

}
