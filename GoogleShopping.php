<?php
require_once 'curl.php';
class GoogleShopping {


////////////////////////////////////////////////////////
//constructor
////////////////////////////////////////////////////////
	public function __construct()
	{
	}
////////////////////////////////////////////////////////
//destructor
////////////////////////////////////////////////////////
	public function __destruct()
  {
		if(isset($this->image))
    {
			imagedestroy($this->image);			
		}
	}	
	
////////////////////////////////////////////////////////
//public methods
////////////////////////////////////////////////////////
	public function getPrices($ean) {
		$curl = new Curl();
		$url= "https://www.google.nl/search?hl=nl&output=search&tbm=shop&q=";
		$ean = str_pad($ean, 14, '0', STR_PAD_LEFT);
		$url = $url . $ean;
		$prices = array();
		$html = $curl->get($url);
		$doc = new DOMDocument();
		libxml_use_internal_errors(True);
		if(!empty($html)){
			$doc->loadHTML($html);
			libxml_clear_errors(); 
			$link = "";
			$arr = $doc->getElementsByTagName("a"); 
			for ($i = 0; $i < $arr->length; $i++) {
				$item = $arr->item($i);
				$href = $item->getAttribute("href");
				$pos = strpos($href, "/shopping/product/");
				if ($pos !== false) {
					$link = $href;
					$i = $arr->length + 1;
				}	
			}
			$pos = strpos($link, "?");
			if ($pos !== false) {
				$link = substr($link, 0, $pos);
			}
			$url = "https://www.google.nl" . $link . "/online?hl=nl";
			unset($doc);
			$html = $curl->get($url);
			$doc = new DOMDocument();
			$prices = array();
			if(!empty($html)){ //if any html is actually returned
				$doc->loadHTML($html);
				libxml_clear_errors(); //remove errors for yucky html
				$xpath = new DOMXpath($doc);
				$articles = $xpath->query('//tr[@class="os-row"]');
				foreach($articles as $osrow) {
					$arr = $osrow->getElementsByTagName("td");
					$prices[] = array(
						'seller' => trim($arr->item(0)->nodeValue),
						'price' => preg_replace("/[^0-9,.]/","",$arr->item(4)->nodeValue)
					);
				}
			}
		}
		return $prices;
	}
}
?>
