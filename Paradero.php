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
 * Include GenericUtils class
 */
require_once 'GenericUtils.php';

/**
 * Include AbstractCURL class
 */
require_once 'AbstractCURL.php';

/**
 * Esta clase contiene las conexiones a Paraderos.
 *
 * @author Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 * @version 1.0
 * @package apis-servicios
 * @copyright Copyright (C) 2010-2013 Pablo Sepúlveda P.
 * @license GNU GPLv3 or later
 * @link http://www.psep.cl
 */
class Paradero extends AbstractCURL {
	private $claveIngresarParadero;
	private $claveBusquedaBus;

	/**
	 * Constructor de la clase
	 *
	 * @param $claveIngresarParadero
	 * @param $claveBusquedaBus
	 */
	public function __construct($claveIngresarParadero, $claveBusquedaBus) {
		$this -> claveIngresarParadero = $claveIngresarParadero;
		$this -> claveBusquedaBus = $claveBusquedaBus;
	}
	
	public function getLogicData(){
		if($this -> claveIngresarParadero != "" && $this -> claveBusquedaBus != ""){
			return $this->searchParaderoAndBus();
		}else if($this -> claveIngresarParadero != ""){
			return $this->searchIngresoParadero();
		}else{
			return null;
		}
	}

	/**
	 *
	 */
	private function searchIngresoParadero() {
		if ($this -> claveIngresarParadero == "") {
			return null;
		} else {
			$url = "http://web.smsbus.cl/simtweb/buscarAction.do?d=busquedaParadero&destino_nombre=rrrrr&servicio=-1&destino=-1&paradero=-1&busqueda_rapida=PC616+C08&ingresar_paradero=" . trim($this -> claveIngresarParadero);
			return $this -> parsedHTML($this -> getData($url), TRUE);
		}
	}

	/**
	 *
	 */
	private function parsedHTML($data, $type) {
		$dataArr = array();
		$data = strip_tags(trim($data), "<div><img>");
		$data = str_replace("\t", "", $data);
		$data = str_replace("\r", "", $data);
		$start= '';
		$end  = '';
		
		if($type){
			$start	= '<div id="contenido_respuesta_2">';
			$end	= '<div style="clear:both"></div>';
		}else{
			$start	= '<div id="contenido_respuesta">';
			$end	= '<div id="volver"';
		}
		
		$parsedData = trim(@GenericUtils::searchTags($data, $start, $end));
		
		if($parsedData == ""){
			if(!$type){
				$start	= '<div id="contenido_respuesta_2">';
				$end	= '<div style="clear:both"></div>';
			}else{
				$start	= '<div id="contenido_respuesta">';
				$end	= '<div id="volver"';
			}
			
			$parsedData = trim(@GenericUtils::searchTags($data, $start, $end));
		}
		
		$dataArr[] = $parsedData;
		
		return json_encode($dataArr);
	}

	/**
	 *
	 */
	private function searchParaderoAndBus() {
		if ($this -> claveIngresarParadero == "" || $this -> claveBusquedaBus == "") {
			return null;
		} else {
			$url = "http://web.smsbus.cl/simtweb/buscarAction.do?d=busquedaRapida&destino_nombre=rrrrr&servicio=-1&destino=-1&paradero=-1&busqueda_rapida=" . trim($this -> claveIngresarParadero) . "+" . trim($this -> claveBusquedaBus) . "&ingresar_paradero=PC616";
			return $this -> parsedHTML($this -> getData($url), FALSE);
		}

	}

	/**
	 *
	 */
	private function getData($urlData) {
		$url = "http://web.smsbus.cl/simtweb/buscarAction.do?d=cargarServicios";
		$cookie = tempnam("/tmp", "cookie");
		$ch = $this -> initialCURL();
		$web = $this -> getOptionalCURL($ch, $cookie, $url);
		$web = $this -> getOptionalCURL($ch, $cookie, $urlData);
		$this -> closeCURL($ch);

		return $web;
	}

}

/**
 * Cabeceras para el JSON
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

/**
 * Paradero.php?busquedaParadero=xxxx&busquedaBus=xxxx
 */
$paradero = new Paradero(trim(htmlspecialchars($_GET["busquedaParadero"])), trim(htmlspecialchars($_GET["busquedaBus"])));
print_r($paradero->getLogicData());

?>