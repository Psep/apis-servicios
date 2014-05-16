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
 * Include AbstractCURL class
 */
require_once 'services/Bip.php';

/**
 * Cabeceras para el JSON
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

$bip = htmlspecialchars($_GET["numberBip"]);

/**
 * Instancia de la clase Bip incluyendo
 * método GET como valor:
 * 
 * BipService.php?numberBip=xxxxxxx
 * 
 * Donde xxxxxxx es el número de la tarjeta.
 */
$service = new Bip($bip);

/**
 * Ejecución de la función getData.
 */
print_r($service->getData());

?>