<?php
class GeoLocation implements JsonSerializable
{
    private $lat;
    private $lon;
    
    public function __construct($lat  = 0, $lon = 0)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }  

    public function get($var_name) { return $this->$var_name; }
    public function set($var_name, $value){ $this->$var_name = $value; }    
    
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
?>
