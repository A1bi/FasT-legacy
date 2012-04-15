<?php
include('include/main.php');

$response = array(
	"error" => "",
	"data" => array()
);
$data = &$response['data'];

switch ($_GET['action']) {
	case "weather":
		$file = "./media/cache/weather.json";
		$cache = json_decode(@file_get_contents($file), true);
		if (!$cache || (int)$cache['time'] + 3600 < time()) {
		
			$locId = 664471;
			$infos = array("temp", "text", "code");

			$xml = new SimpleXMLElement("http://weather.yahooapis.com/forecastrss?u=c&w=".$locId, null, true);
			$yweather = $xml->channel->item->children("yweather", true);
			$condition = $yweather->condition->attributes();
			$forecast = $yweather->forecast->attributes();
			$data['low'] = (string)$forecast['low'];
			$data['high'] = (string)$forecast['high'];
			$data['daytime'] = (date("H") > 6 && date("H") < 21) ? "d" : "n";
			$data['date'] = strtotime($condition['date']);

			foreach ($infos as $info) {
				$data[$info] = (string)$condition[$info];
			}

			$rawData = file_get_contents("http://www.worldweatheronline.com/Kaisersesch-weather/Rheinland-Pfalz/DE.aspx");
			preg_match('#<div class="lhs2">P.O.P: </div><div class="rhs2">([0-9]+)%</div>#isU', $rawData, $info);
			$data['pop'] = $info[1];
			
			$save = array(
				"time" => time(),
				"data" => $data
			);
			$f = @fopen($file, "w+");
			@fwrite($f, json_encode($save));
			@fclose($f);
			
		} else {
			$data = $cache['data'];
		}
		
		$data['date'] = date("H:i", $data['date']);
		
		break;
	
	default:
		$response['error'] = "unknown action";
}

echo json_encode($response);

?>
