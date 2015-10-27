<?php
require_once 'models/GeoLocation.php';

class GeoCoder
{
    public function geoCode($address)
    {
        $api_key = "AIzaSyBSXsuJ9eK4Nb1IuPP-NTEpmEIjR1Fhxgo";
        $base_url = "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&key=$api_key&";
        $address = str_replace (" ", "+", urlencode($address));
        $url = $base_url."address=$address";
        
        $curl_handler = curl_init();
        curl_setopt($curl_handler, CURLOPT_URL, $url);
        curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($curl_handler), true);
        
        if($response['status'] != 'OK')
        {
            return null;
        }    
        $location = new GeoLocation();
        
        if( isset($response['results'][0]['geometry']['location']['lng']) ) 
            $location->set("lon", $response['results'][0]['geometry']['location']['lng'] );
        if( isset($response['results'][0]['geometry']['location']['lat']) )
            $location->set("lat", $response['results'][0]['geometry']['location']['lat'] );
        
        return $location;
    }
}
?>
