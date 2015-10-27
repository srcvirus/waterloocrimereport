<?php

require_once ('Parser.php');
require_once ('GeoCoder.php');
require_once ('models/daily_call_summary.php');

class MajorIncidentsParser extends ABSParser
{
    private $patterns;
    private $begin_div_patter, $end_div_pattern;
    private $n_patterns;
    private $INCIDENT_TITLE_INDEX = 2;
    private $INCIDENT_ID_INDEX = 3;
    private $INCIDENT_DATE_INDEX = 4;
    private $INCIDENT_LOC_INDEX = 5;

    public function __construct($file_name)
    {
	    $this->file_name = $file_name;
        $this->patterns[] = '/<div class="incidents">/';
        $this->patterns[] = '/<div class="incident .*">/';
        $this->patterns[] = '/<div class="incidenttype">(.*)<\/div>/';
        $this->patterns[] = '/<div class="incidentlocation"><strong>Incident #: <\/strong>(.*)<\/div>/';
        $this->patterns[] = '/<div class="incidentdate"><strong>Incident Date: <\/strong>(.*)<\/div>/';
        $this->patterns[] = '/<div class="incidentlocation"><strong>Location: <\/strong>(.*)<\/div>/';

        $this->n_patterns = count($this->patterns);
	    $this->begin_div_pattern = "/<div>/";
        $this->end_div_pattern = "/<\/div>/";

    }

    public function parse()
    {
        $page = fopen($this->file_name, "r");
        $i = 0;
        $dcs = new DailyCallSummary();
        $incident_set = array();
        $geocoder = new GeoCoder();
        
        while(($page_data = fgets($page)))
        {
            if(preg_match($this->patterns[$i], $page_data, $matches))
            {
                    if($i >= 2)
                    {
                        switch($i)
                        {
                            case $this->INCIDENT_TITLE_INDEX :
                                $dcs = new DailyCallSummary();
                                $dcs->set("title", $matches[1]);
                                break;

                            case $this->INCIDENT_ID_INDEX :
                                $dcs->set("_id", $matches[1]);
                                break;

                            case $this->INCIDENT_DATE_INDEX :
                                $dcs->set("date", $matches[1]);
                                break;

                            case $this->INCIDENT_LOC_INDEX :
                                $dcs->set("intersection", $matches[1]);
                                $location = $geocoder->geocode($dcs->get("intersection"));
                                if($location)
                                    $dcs->set("lat", $location->get("lat"))->set("lon", $location->get("lon"));
                                break;
                        }
                    }
                    $i++;

                    if($i >= $this->n_patterns)
                    {
                        $incident_set []= $dcs;
                        $i = 1;
                    }
            }
        }
        fclose($page);
        return $incident_set;
    }
}
