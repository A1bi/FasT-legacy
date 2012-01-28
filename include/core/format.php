<?php
/**
 * PVshowcase
 *
 * String formatting class
 */
class format {

	/**
	 * Parses format code in strings
	 *
	 * @param string $text
	 * @return string
	 */
	private function parse($text) {
		$text = str_replace(" ", "&#160;", $text);
		$text = preg_replace("!\[f\](.*)\[/f\]!isU", "<strong>$1</strong>", $text);
		$text = preg_replace("!\[k\](.*)\[/k\]!isU", "<em>$1</em>", $text);
		$text = preg_replace("!\[u\](.*)\[/u\]!isU", "<u>$1</u>", $text);
		$text = preg_replace("!\[color=(.*)\](.*)\[/color\]!isU", "<span style=\"color: $1;\">$2</span>", $text);
		
		return $text;
	}

	/**
	 * rounds value to given number of decimal places
	 * 
	 * @param mixed(int, float) $value
	 * @param int $roundto
	 * @return float
	 */
	public function value($value, $roundto = 0) {
		$value = number_format((float)$value, $roundto, ",", ".");
		return $value;
	}

	/**
	 * Does different things with strings
	 *
	 * @param string $var
	 * @param array $options
	 * @return string
	 */
	public function single($var, $options) {
		foreach ($options as $option) {
			switch ($option) {

				case "codes":
					$var = self::parse($var);
					break;

				case "nl":
					$var = nl2br($var);
					break;

				case "esc":
					$var = stripslashes(htmlspecialchars($var));
					break;

				case "sql":
					$var = addslashes($var);
					break;

				case "date":
					$time = $var;
					if ($time == 0) {
						$var = "nie";
					} else {
						switch (date("d.m.Y", $time)) {
							case date("d.m.Y"):
								$var = "Heute";
								break;
							case date("d.m.Y", time()-86400):
								$var = "Gestern";
								break;
							default:
								$var = date("d.m.Y", $time);
						}
						$var .= ", " . date("H:i", $time) . " Uhr";
					}
			
			}
		}

		return $var;
	}

	/**
	 * Runs through a complete array to format every child
	 *
	 * @param mixed (array, string) $var
	 * @param array $options
	 * @return mixed (array, string)
	 */
	public function complete($var, $options) {
		if (empty($var)) return $var;

		if ((is_array($var) || is_object($var)) && count($var)>=1) {
			$nvar = array();
			foreach ($var as $key => $value) {
				$nvar[$key] = self::complete($value, $options);
			}
		} else {
			$nvar = self::single($var, $options);
		}

		return $nvar;
	}

}
?>
