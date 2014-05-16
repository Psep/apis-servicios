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
 * Clase de Consulta Tarjeta Bip!.
 * 
 * @author Pablo Sepúlveda P. <psep.pentauc@gmail.com>
 * @version 1.0
 * @package apis-servicios
 * @copyright Copyright (C) 2010-2013 Pablo Sepúlveda P.
 * @license GNU GPLv3 or later
 * @link http://www.psep.cl
 */
class Bip extends AbstractCURL{

	/**
	 * Atributo privado donde recaerá el valor del
	 * ID de la tarjeta BIP!.
	 */
	private $idNumber;

	/**
	 * Constructor de la clase
	 * 
	 * @param $idNumber
	 */
	public function __construct($idNumber) {
		$this->idNumber = $idNumber;
	}

	/**
	 * Función que retorna la data de la tarjeta
	 * en formato json. Si no hay data retorna null.
	 * 
	 * @return json
	 */
	public function getData() {
		if ($this -> idNumber == null && $this -> idNumber == "") {
			return null;
		} else {
			$url	= "http://200.6.67.22/PortalCAE-WAR-MODULE/SesionPortalServlet?accion=6&NumDistribuidor=99&NomUsuario=usuInternet&NomHost=AFT&NomDominio=aft.cl&Trx=&RutUsuario=0&NumTarjeta=" . $this -> idNumber . "&bloqueable=";
			$data	= GenericUtils::searchTags($this->getCURL($url), '<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">', '</table>');
			$dom 	= new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->loadHTML($data);
			$colums = $dom->getElementsByTagName('td');
			$dataArr= array();
			$name	= "";
			
			for ($i = 0; $i < $colums->length; $i++) {
				$objDOM = $colums->item($i);
				
				if($name == ""){
					$name = substr(trim(htmlentities($objDOM->textContent)), 0, -1);
				}else{
					$dataArr[$name] = trim(htmlentities($objDOM->textContent));
					$name = "";
				}

			}
			
			if(count($dataArr) < 4){
				return null;
			}

			return json_encode($dataArr);
		}

	}

}

/**
 * Cabeceras para el JSON
 */
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

/**
 * Instancia de la clase Bip incluyendo
 * método GET como valor:
 * 
 * Bip.php?numberBip=xxxxxxx
 * 
 * Donde xxxxxxx es el número de la tarjeta.
 */
$bip = new Bip(htmlspecialchars($_GET["numberBip"]));

/**
 * Ejecución de la función getData.
 */
print_r($bip -> getData());

?>