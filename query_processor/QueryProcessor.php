<?php
require_once 'controllers/DailyCallSummaryController.php';

class QueryProcessor
{
    public static function getDailyIncidentsFromTime($from_time)
    {
        $from_time = str_replace ("+", " ", urldecode($from_time));
        $retObjs = DailyCallSummaryController::getDailySummaryFromDate($from_time);
        return $retObjs;
    }
    
    public static function getDailyIncidentsDateRange($from_time, $to_time)
    {
        $from_time = str_replace ("+", " ", urldecode($from_time));
        if(!strcmp($to_time, 'now'))
        {
            date_default_timezone_set("America/Toronto");
            $to_time = date('c');
        }
        else $to_time = str_replace("+", " ", urldecode($to_time));
        
        $retObjs = DailyCallSummaryController::getDailySummaryDateRange($from_time, $to_time);
        return $retObjs;
    }
    
    public static function getDailyIncidentsMonthAggregate($month_year)
    {
        $month_year = str_replace("+", " ", urldecode($month_year));
        $month = split(' ', $month_year)[0];
        $year = split(' ', $month_year)[1];
        $retObjs = DailyCallSummaryController::getDailyIncidentsMonthAggregate($month, $year);
        return $retObjs;
    }
    
    public static function getIncidentsAggregate($incident_type, $from_time, $to_time)
    {
        $incident_type = str_replace("+", " ", urldecode($incident_type));
        $from_time = QueryProcessor::processTime($from_time);
        $to_time = QueryProcessor::processTime($to_time);
        $retObjs = DailyCallSummaryController::getIncidentsAggregate($incident_type, $from_time, $to_time);
        return $retObjs;    
    }

    public static function getIncidentTypes()
    {
        $retObjs = DailyCallSummaryController::getIncidentTypes();
        return $retObjs;
    }
    
    private static function processTime($time)
    {
        if(!strcmp($time, 'now'))
        {
            date_default_timezone_set('America/Toronto');
            $time = date('c');
        }
        else $time = str_replace("+", " ", urldecode($time));
        return $time;
    }
    
    

}
?>
