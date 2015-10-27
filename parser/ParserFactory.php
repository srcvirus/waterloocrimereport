<?php
require_once 'DailySummaryParser.php';
require_once 'MajorIncidentsParser.php';

class ParserFactory
{
    public static function getParser($incident_type, $file_name)
    {
        $ret = null;
        
        switch($incident_type)
        {
            case 'daily-call-summary':
                $ret = new DailySummaryParser($file_name);
                break;
            case 'current-incidents':
                break;
            case 'major-incidents':
                $ret = new MajorIncidentsParser($file_name);
                break;
        }
        return $ret;
    }
}
?>
