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
 * Include GenericUtils class
 */  
require_once 'utils/GenericUtils.php'; 

/**
 * Include AbstractCURL class
 */
require_once 'services/AbstractCURL.php';

/**
 * Include Restaurant class
 */
require_once 'services/Restaurant.php';

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
 * RestaurantService.php?region=xxxxx
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