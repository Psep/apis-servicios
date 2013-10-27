<?php

/**
 * @author psep
 * @license GPL v3
 */
final class GenericUtils{
	
	/**
	 * Función que retorna una búsqueda de un string
	 * definida por los parámetros de inicio y fin.
	 * 
	 * @param $string
	 * @param $start
	 * @param $end
	 * @return string
	 */
	public static function searchTags($string, $start, $end) {
		$string = " " . $string;
		$ini	= strpos($string, $start);
		if ($ini == 0)return "";
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return trim(substr($string, $ini, $len));
	}
	
}

?>
