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
 * Include Paradero class
 */
require_once 'services/Paradero.php';

/**
 * Se define una variable para la data de buses.
 */
define('DATA_BUSES', 'data/buses.json');

/**
 * Cabeceras para el JSON
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

$paradero = trim(htmlspecialchars($_GET["busquedaParadero"]));
$bus = trim(htmlspecialchars($_GET["busquedaBus"]));

/**
 * ParaderoService.php?busquedaParadero=xxxx&busquedaBus=xxxx
 */
$service = new Paradero($paradero, $bus);
print_r($service->getLogicData());

?>