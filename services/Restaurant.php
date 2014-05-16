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
 * Service Restaurant's class by OpenGov's data.
 * 
 * @author Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 * @version 1.0
 * @package apis-servicios
 * @copyright Copyright (C) 2010-2014 Pablo Sepúlveda P.
 * @license GNU GPLv3 or later
 * @link http://www.psep.cl
 */
class Restaurant extends AbstractCURL {
	
    /**
     * Static String attribute with url and api key for json service.
     */
    private static $httpClient = "http://api.recursos.datos.gob.cl/datastreams/invoke/RESTA-O-SIMIL?auth_key=aee437a36dac88ec8e2df3093915e02c05db27d0&output=json_array";
    
    /**
     * String String attribute for load region.
     */
    private $region;
    
    /**
     * This is the class's constructor
     * 
     * @param String $region
     */
	function __construct($region) {
		$this->getRegion($region);
	}
    
    /**
     * This function load the formal region by code 
     * in data json.
     * 
     * @param String $region
     */
    public function getRegion($region) {
        switch ($region) {
            case 'metropolitana':
                $this->region = "METROPOLITANA DE SANTIAGO";
                break;
                
            case 'antofagasta':
                $this->region = "ANTOFAGASTA";
                break;
                
            case 'arica':
                $this->region = "ARICA Y PARINACOTA";
                break;
                
            case 'atacama':
                $this->region = "ATACAMA";
                break;
                
            case 'aysen':
                $this->region = "AYS�N";
                break;
            
            case 'biobio':
                $this->region = "B�O-B�O";
                break;    
            
            case 'coquimbo':
                $this->region = "COQUIMBO";
                break;
         
            case 'araucania':
                $this->region = "LA ARAUCAN�A";
                break;
            
            case 'ohiggins':
                $this->region = "LIB. GRAL. B. O'HIGGINS";
                break;
            
            case 'lagos':
                $this->region = "LOS LAGOS";
                break;
            
            case 'rios':
                $this->region = "LOS R�OS";
                break;
            
            case 'magallanes':
                $this->region = "MAGALLANES";
                break;
            
            case 'maule':
                $this->region = "MAULE";
                break;
          
            case 'tarapaca':
                $this->region = "TARAPAC�";
                break;
            
            case 'valparaiso':
                $this->region = "VALPARA�SO";
                break;
            
            default:
                $this->region = null;
                break;
        }
    }
    
    /**
     * This function returns the json data process with
     * filters by region.
     * 
     * @return json
     */
    public function getData() {
        if ($this->region == null || trim($this->region) == "") {
            return null;
        } else {
            $json = $this->getCURL(static::$httpClient);
            $array= json_decode($json);
            
            /*
             * Data structure:
             * 
                0 => string 'Región' (length=8)
                1 => string 'Provincia' (length=9)
                2 => string 'Comuna' (length=6)
                3 => string 'Localidad' (length=9)
                4 => string 'Destino' (length=7)
                5 => string 'Nombre Establecimiento' (length=22)
                6 => string 'Calle' (length=5)
                7 => string 'Numero' (length=6)
                8 => string 'Otro' (length=4)
                9 => string 'Rut' (length=3)
                10 => string 'Fono Gerencia o Adm.' (length=20)
                11 => string 'Web' (length=3)
                12 => string 'Clase' (length=5)
            */
            
            $data= array();
            $aux = false;
            
            foreach ($array->result as $key => $obj) {
                if ($aux) {
                    if (trim($obj[0]) == $this->region) {
                        $list = array(
                                'region' => trim($obj[0]), 
                                'comuna' => trim($obj[2]), 
                                'nombre' => trim($obj[5]), 
                                'direccion' => trim($obj[6])." ".trim($obj[7]),
                                'fono' => trim($obj[10]),
                                'web' => trim($obj[11]),
                                'tipo' => trim($obj[12])
                             );
                
                        $data[] = $list;
                    }
                }
                
                $aux = true;
            }
            
            return $data;
            // return json_encode($data);
        }
    }
}

/**
 * JSON's headers
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

/**
 * Load GET parameter region
 */
$region = htmlspecialchars(strtolower($_GET["region"]));

/**
 * Restaurant's instance.
 * 
 * Load service with:
 * 
 * Restaurant.php?region=xxxxx
 * 
 * Options (xxxxx parameter):
 * 
 * metropolitana, antofagasta, arica, atacama, aysen, biobio, 
 * coquimbo, araucania, ohiggins, lagos, rios, magallanes, 
 * maule, tarapaca, valparaiso
 */
$service= new Restaurant($region);

/**
 * Execute the service
 */
print_r($service->getData());

?>