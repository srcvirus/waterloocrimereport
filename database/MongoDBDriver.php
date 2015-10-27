<?php
require_once 'DBDriver.php';

class MongoDBDriver extends DBDriver
{
    public function __construct($db_host)
    {
        $this->db_host = $db_host;
    }
    
    public function connect()
    {
        $this->db_connection = new Mongo("mongodb://".$this->db_host);
    }
    
    public function close()
    {
        $this->db_connection->close();
    }
    
    public function select_db($db_name)
    {
        $this->selected_db = $this->db_connection->$db_name;
    }    
       
    public function insert_query($table_name, $object, $batch_insert = false)
    {
	    if(!$batch_insert)
	            $this->selected_db->$table_name->insert($object->jsonSerialize());
	    else
		    $this->selected_db->$table_name->batchInsert($object);
    }
    
    public function select_query($table_name, $query, $aggregate_query = false)
    {
        $retObjs = array();
        if(!$aggregate_query)
        {
            $cursor = $this->selected_db->$table_name->find($query);
            foreach($cursor as $obj)
                $retObjs[] = $obj;  
        }
        else
        {
            $retArray = $this->selected_db->$table_name->aggregate($query);
            if($retArray['ok'] == 1)
            {
                foreach($retArray['result'] as $obj)
                    $retObjs[] = $obj;
            }
        }    
           
        return $retObjs;
    }
    
    public function update_query($table_name, $object)
    {
        ;
    }
    
    public function delete_query($table_name, $object)
    {
        ;
    }
}
?>
