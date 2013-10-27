<?php
/**
 * Copyright (C) 2013 Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 *
 * This file is part of apis-servicios.
 * apis-servicios is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * apis-servicios is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with apis-servicios.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * This class cointains utils statics functions.
 * @author Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 * @version 1.0
 * @package apis-servicios
 * @copyright Copyright (C) 2010-2013 Pablo Sepúlveda P.
 * @license GNU GPLv3 or later
 * @link http://www.psep.cl
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
