<?php

/**
 * A simple ad server/rotator with geotargeting functionalities based on user IP address.
 * Uses free freegeoip.app API to convert IP to Geo.
 * 
 * You need to create a textual file for each ad size containing all ads codes separated by tilde (~). Codes in default files will be delivered to any country.
 * e.g. ad_default_300x250.txt -> contains "default" ads in 300x250 format
 *      ad_IT_300x250.txt -> contains ads for users in Italy in 300x250 format
 *      ad_ES_300x250.txt -> contains ads for users in Spain in 300x250 format
 *      ad_default_300x600.txt -> contains "default" ads in 300x600 format
 * 
 * @author Yes We Web < www.yesweweb.com >
 */
 
const VERSION = "0.0.1"; //BETA version!

class GeoAdRotator {
	
	const FILE_DIR = "codes/";
	const FILE_PREFIX = "ad_";
	const FILE_EXT = ".txt";
	
	public function serve($size = "responsive", $quantity = 1) {
		$loadAds = $this->loadAds($size);

		if (!empty($loadAds)) :
			if ($quantity > 1) :
				$arrayPart = array_splice($loadAds, 0, $quantity);
				$adCode = implode("<br>", $arrayPart);
			else :
				$adCode = $loadAds[0];
			endif;
			
			return $adCode;
		endif;
		
		return false;
	}
	
	private function curlGeoIp($ip = null) {
		$geoIpUrl = "https://freegeoip.app/json/";
		if (isset($ip))
			$geoIpUrl .= $ip;
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $geoIpUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"Content-Type: application/json"
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);
		
		if ($err) :
			echo "cURL Error #:" . $err;
		else :
			return $response;
		endif;
	}
	
	private function getUserIpAddress() {
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	        //ip from share internet
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	        //ip pass from proxy
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}
	
	private function getUserGeo() {
		$json = $this->curlGeoIp( $this->getUserIpAddress() );
		$data = json_decode($json);

		return $data;
	}
	
	public function getUserGeoCountryName() {
		$geoData = $this->getUserGeo();
		return $geoData->country_name;
	}
	
	public function getUserGeoCountryCode() {
		$geoData = $this->getUserGeo();
		return $geoData->country_code;
	}
	
	public function loadAds($size = null) {
		$groups[] = "default";
		if (null !== $this->getUserGeoCountryCode())
			$groups[] = $this->getUserGeoCountryCode();
		
		$ads = [];
		foreach ($groups as $group) :
			$fileName = __DIR__ . "/";
			$fileName .= self::FILE_DIR . self::FILE_PREFIX . $group;
			if ($size)
				$fileName .= "_" . $size;
			$fileName .= self::FILE_EXT;
			

			if (file_exists($fileName)) :
				$fcontents = file_get_contents($fileName);
				$ad_array = explode("~", $fcontents);
				array_push($ads, ...$ad_array);
			endif;
		endforeach;
		
		shuffle($ads);
		
		return $ads;
	}
}
