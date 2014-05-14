<?php
/**
 * Copyright (C) 2014 Pablo Sepúlveda P. <psep.pentauc@gmail.com>
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
 * Import GenericUtils class. 
 */
require_once 'GenericUtils.php';

/**
 * Import AbstractCURL abstract class.
 */
require_once 'AbstractCURL.php';

/**
 * Service Farmacia's class by OpenGov's data.
 * 
 * @author Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 * @version 1.0
 * @package apis-servicios
 * @copyright Copyright (C) 2010-2014 Pablo Sepúlveda P.
 * @license GNU GPLv3 or later
 * @link http://www.psep.cl
 */
class Farmacia extends AbstractCURL {
    
    private static $turno = "http://api.recursos.datos.gob.cl/datastreams/invoke/FARMA-DE-TURNO-EN-LINEA?auth_key=aee437a36dac88ec8e2df3093915e02c05db27d0&output=json_array";
    private static $factor = 0.02;
    
    private $latX;
    private $latY;
    private $lonX;
    private $lonY;
    
    private $lat;
    private $lon;
	
	function __construct($lat, $lon) {
		$this->lat = $lat;
        $this->lon = $lon;
        $this->loadLimits();
	}
    
    public function loadLimits() {
        // Right
        $this->lonY = $this->lon + static::$factor;
        
        // Left
        $this->lonX = $this->lon - static::$factor;
        
        // Up
        $this->latX = $this->lat + static::$factor;
        
        // Down
        $this->latY = $this->lat - static::$factor;
    }
    
    public function getData() {
        if ($this->lat == null || $this->lon = null
                || trim($this->lat) == "" || trim($this->lon) == "") {
            return null;
        }
        
        $json  = $this->getCURL(static::$turno);
        $object= json_decode($json); 
        
        /**
            0 => string 'ID RegiÃ³n' (length=10)
            1 => string 'ID Comuna' (length=9)
            2 => string 'DirecciÃ³n' (length=10)
            3 => string 'Nombre de Farmacia' (length=18)
            4 => string 'Horario Apertura' (length=16)
            5 => string 'Horario de Cierre' (length=17)
            6 => string 'Latitud' (length=7)
            7 => string 'Longitud' (length=8)
            8 => string 'TelÃ©fono' (length=9)
        */
        
        $data = array();
        $aux = false;

        foreach ($object->result as $key => $obj) {
            if ($aux) {
                $latObj = trim($obj[6]);
                $lonObj = trim($obj[7]);
                if ($lonObj <= $this->lonY && $lonObj >= $this->lonX && $latObj <= $this->latX && $latObj >= $this->latY) {
                    $list = array(
                                    'direccion' => trim($obj[2]),
                                    'nombre' => trim($obj[3]),
                                    'apertura' => trim($obj[4]),
                                    'cierre' => trim($obj[5]),
                                    'lat' => trim($obj[6]),
                                    'lon' => trim($obj[7]),
                                    'fono' => trim($obj[8])
                            );

                    $data[] = $list;
                }
            }

            $aux = true;
        }

        return $data;
    }

}

/**
 * JSON's headers
 */
// header("Access-Control-Allow-Origin: *");
// header('Content-type: application/json');

/**
 * Load GET parameter lat and lon
 */
$lat = htmlspecialchars($_GET["lat"]);
$lon = htmlspecialchars($_GET["lon"]);

/**
 * Farmacia's instance.
 * 
 * Load service with:
 * 
 * Farmacia.php?lat=xxxx&lon=xxxx
 * 
 */
$lat = "-33.449069";
$lon = "-70.654828";
$service = new Farmacia($lat, $lon);

/**
 * Execute the service
 */
// print_r($service->getData());
var_dump($service->getData());
// var_dump($service);

?>