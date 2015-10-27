<?php

class ControllerFactory
{
    public static function getController($incident_type)
    {
        $ret = null;
        
        switch($incident_type)
        {
            case 'daily-call-summary':
                $ret = "DailyCallSummaryController";
                break;
            case 'current-incidents':
                break;
            case 'major-incidents':
                $ret = "MajorIncidentController";
                break;
        }
        return $ret;
    }
}
?>
