<?php
require_once 'config/datamagic.conf.php';
require_once 'models/major-incident.php';
require_once 'database/MongoDBDriver.php';

class MajorIncidentController
{
    private $mongodb_driver;
    
    public static function create($object)
    {
        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        $mongodb_driver->insert_query('major_incident', $object);
    }
    
    public static function createFromCollection($collection)
    {
        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        foreach($collection as $obj)
            $mongodb_driver->insert_query('major_incident', $obj);
        $mongodb_driver->close();
    }
    
    public static function getMajorIncidentFromDate($str_date)
    {
        $mongo_date = new MongoDate(strtotime($str_date));
        
        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        
        $query = array();
        $query["date"] = array('$gt' => $mongo_date);
        
        $result_set = $mongodb_driver->select_query('major_incident', $query);
        return $result_set;
    }
    
    public static function getMajorIncidentDateRange($from_date, $to_date)
    {
        $mongo_from_date = new MongoDate(strtotime($from_date));
        $mongo_to_date = new MongoDate(strtotime($to_date));
        
         $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        
        $query = array();
        $query["date"] = array('$gte' => $mongo_from_date, '$lte' => $mongo_to_date );
        
        $result_set = $mongodb_driver->select_query('major_incident', $query);
        return $result_set;
    }
    
    public static function getMajorIncidentsMonthAggregate($month, $year)
    {
        $month_timestamp = strtotime("$month $year");

        $month_first_second = date('Y-m-01 00:00:00 T', $month_timestamp);
        $month_last_second  = date('Y-m-t 23:59:59 T', $month_timestamp);
        
        $mongo_from_time = new MongoDate(strtotime($month_first_second));
        $mongo_to_time = new MongoDate(strtotime($month_last_second));

        $query[] = array('$match' => array('date' => array('$gte' => $mongo_from_time, '$lte' => $mongo_to_time)));
        $query[] = array('$group' => array('_id' => array('type' => '$title'), 
                                           'count' => array('$sum' => 1)));

        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        $result_set = $mongodb_driver->select_query('major_incident', $query, true);

        return $result_set;
    }
    
    public static function getMajorIncidentsAggregate($incident_type, $from_time, $to_time)
    {
        $match = array();
        if(strcmp($incident_type, 'all') != 0 && strlen($incident_type) > 0)
            $match['title'] = $incident_type;
        
        $mongo_to_time = new MongoDate(strtotime($to_time));
        $match['date']['$lte'] = $mongo_to_time;
        
        if(strcmp($from_time, 'begin') != 0 && strlen($incident_type) > 0)
        {
            $mongo_from_time = new MongoDate(strtotime($from_time));
            $match['date']['$gte'] = $mongo_from_time;
        }
        
        $query[] = array('$match' => $match);
        $query[] = array('$group' => array('_id' => array('type' => '$title'),
                                           'count' => array('$sum' => 1),
                                           'dates' => array('$push' => '$date'),
                                           'locations' => array('$push' => array('lat' => '$lat', 'lon' => '$lon'))));
        //echo json_encode($query);
        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        $result_set = $mongodb_driver->select_query('major_incident', $query, true);    

        return $result_set;

    }
    
    public static function getMajorIncidentTypes()
    {
        $query[] = array('$group' => array('_id' => array('type' => '$title')));
        $mongodb_driver = new MongoDBDriver('localhost');
        $mongodb_driver->connect();
        $mongodb_driver->select_db('datamagic');
        $result_set = $mongodb_driver->select_query('major_incident', $query, true);    
        return $result_set;   
    }
}

// select title, COUNT(*) from daily_call_summary where date in () group by title
?>
