<?php

require_once 'GenericUtils.php';

/**
 * @author psep
 * @license GPL v3
 */
class Bip {

	private $idNumber;

	/**
	 * Constructor de la clase
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
			$cookie = tempnam("/tmp", "cookie");
			$ch		= curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://200.6.67.22/PortalCAE-WAR-MODULE/SesionPortalServlet?accion=6&NumDistribuidor=99&NomUsuario=usuInternet&NomHost=AFT&NomDominio=aft.cl&Trx=&RutUsuario=0&NumTarjeta=" . $this -> idNumber . "&bloqueable=");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)");
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
			$web	= curl_exec($ch);
			curl_close($ch);

			$data = GenericUtils::searchTags($web, '<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">', '</table>');

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

$bip = new Bip(htmlspecialchars($_GET["numberBip"]));
print_r($bip -> getData());

?>