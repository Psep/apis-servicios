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
    private $listBuses;

	/**
	 * Constructor de la clase
	 *
	 * @param $claveIngresarParadero
	 * @param $claveBusquedaBus
	 */
	public function __construct($claveIngresarParadero, $claveBusquedaBus) {
		$this->claveIngresarParadero = $claveIngresarParadero;
		$this->claveBusquedaBus = $claveBusquedaBus;
        $this->loadBuses();
    }
	
    /**
     * Función principal que ejecuta las búsquedas según
     * los datos y variables.
     * 
     * @return json
     */
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
     * Método que carga lista de buses
     * desde json a un array..
     */
    private function loadBuses() {
        $json = file_get_contents(DATA_BUSES);
        $data = json_decode($json);
        $list = $data->buses;
        
        foreach ($list as $key => $obj) {
            $this->listBuses[] = $obj->id;
        }
    }

	/**
     * Método que genera la búsqueda de
     * sólo el paradero.
     * 
     * @return json
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
	 * Función que produce el parsing y la generación
     * del json.
     * 
     * @param String $data
     * @param boolean $type
     * @return json
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
		
		$parsedDataList = preg_split('/(<[^>]*[^\/]>)/i', $parsedData, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $list = array();
        $fields = "";
        $i = 0;
        
        foreach ($parsedDataList as $key => $node) {
            $isDiv = strpos($node, "div");
            
            if ($isDiv === false) {
                $singleTrim = trim($node);
                
                if ($singleTrim != "") {
                    // echo $i." - ".$singleTrim."\n";//TODO
                    if ($i == 1) {
                        $list["info"] = $singleTrim;
                    } elseif ($i == 2) {
                        $list["header"] = $singleTrim;
                    }
                    
                    // Valida tipo de consulta sin bus
                    if ($i > 5 && $type == TRUE) {
                        $validate = $this->isBus($singleTrim);
                        
                        if ($validate == TRUE) {
                            // Cabecera
                            if ($i > 7) {
                                $fields.= "</li>";
                            }
                            
                            $fields.= "<li>";
                            $fields.= "<p>";
                            $fields.= $singleTrim;
                            $fields.= "</p>";
                        } else {
                            // Campos
                            if (strpos($singleTrim, "istancia") === FALSE) {
                                $fields.= "<p>";
                                $fields.= $singleTrim;
                                $fields.= "</p>";
                            }
                        }
                    
                    // Tipo de consulta con bus    
                    } elseif ($i > 5 && $type == FALSE) {
                           
                        if ($i == 7) {
                            $fields.= "<li>";
                            $fields.= "<p>";
                            $fields.= $this->claveBusquedaBus;
                            $fields.= "</p>";
                        } else {
                            $fields.= "<p>";
                            $fields.= $singleTrim;
                            $fields.= "</p>";
                        }
                        
                    }
                                        
                    $i++;
                }
            }
        }
        
        $dataList = "<ul>";
        
        if ($fields == "") {
            $dataList.= "<li>";
            $dataList.= "Sin información";
            $dataList.= "</li>";
        } else {
            $isLi = strpos($fields, "li");
            
            if ($isLi === false) {
                $dataList.= "<li>";
            }
            
            $dataList.= $fields;
            $dataList.= "</li>";
        }
        
        $dataList.= "</ul>";
        
        $list["dataList"] = $dataList;
        
        return json_encode($list);
	}
    
    /**
     * Función que valida si la cadena de string
     * ingresada es un bus.
     * 
     * @param String $line
     * @return boolean
     */
    private function isBus($line) {
        foreach ($this->listBuses as $key => $bus) {
            if ($bus == $line) {
                return true;
            }
        }
        
        return false;
    }

	/**
	 * Función que hace la búsqueda de paradero y bus
     * retornando la data en json.
     * 
     * @return json
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
	 * Función que ejecuta la consulta según URL
     * ingresada mediante cURL y retorna el string consumido.
     * 
     * @param String $urlData
     * @return String
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

?>