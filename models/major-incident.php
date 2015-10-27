<?php

class MajorIncident implements JsonSerializable
{
  private $_id;
  private $title;
  private $date; 
  private $intersection; 
  private $lat, $lon;
  private $description;
  
  public function __construct($incidentId = "", $title = "", $date = "", $intersection = "", $lat = 0, $lon = 0, $description = "")
  {
    $this->set("_id", $incidentId)
         ->set("title", $title)
         ->set("date", $date)
         ->set("intersection", $intersection)
         ->set("lat", $lat)
         ->set("lon", $lon);
  }
  
  public function get($var_name) { return $this->$var_name; }
  public function set($var_name, $value){ $this->$var_name = $value; return $this; }
  
  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
?>
