<?php
abstract class DBDriver
{
    protected $db_connection;
    protected $selected_db;
    protected $db_host;
    
    public abstract function connect();
    public abstract function close();
    
    public abstract function select_db($db_name);
    
    public abstract function insert_query($table_name, $object, $batch_insert = false);
    public abstract function select_query($table_name, $query, $aggregate_query = false);
    public abstract function update_query($table_name, $object);
    public abstract function delete_query($table_name, $object);
}
?>
