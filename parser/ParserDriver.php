<?php
require_once 'ParserFactory.php';
require_once 'controllers/DailyCallSummaryController.php';
require_once 'controllers/MajorIncidentController.php';
require_once 'controllers/ControllerFactory.php';

class ParserDriver
{
    public static function start_parsing($incident_type)
    {
        $ret_code["status"] = "success";
        try
        {
            date_default_timezone_set("America/Toronto");
            $base_preprocessed_path = "data/preprocessed";
            $base_processed_path = "data/processed";
            
            $directory_path = "$base_preprocessed_path/$incident_type";
            $processed_file_path = "$base_processed_path/$incident_type";
            
            $directory_listing = new DirectoryIterator($directory_path);
            
            foreach($directory_listing as $file)
            {
                if(!$file->isDot())
                {
                    $parser = ParserFactory::getParser($incident_type, $file->getPathname());
                    $dataObjects = $parser->parse();
                    rename($file->getPathname(), "$processed_file_path"."/".$file->getFilename());
                    $controller = ControllerFactory::getController($incident_type);    
                    $controller::createFromCollection($dataObjects);
                }
            }
        }
        catch (Exception $exception)
        {
            $ret_code["status"] = "fail";
            $ret_code["fail_reason"] = $exception->getMessage();
        }
        
        return $ret_code;
    }
}
?>
