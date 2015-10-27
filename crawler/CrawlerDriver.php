<?php
require_once 'Crawler.php';

class CrawlerDriver
{
    public static function start_crawling($incident_type)
    {
        $ret_code = array("status" => "success");
	    $valid_pages = array("daily-call-summary", "major-incidents", "current-incidents");

	    if(!in_array($incident_type, $valid_pages))
	    {
		    $ret_code = array("status" => "fail",
				    "fail_reason" => "invalid incident type. Valid types are: ".implode(", ",$valid_pages));

		    return $ret_code;
	    }

	    $crawl_url = "http://www.wrps.on.ca/$incident_type";
	    $data_directory = "data/preprocessed/$incident_type";

	    $url_list [] = $crawl_url;
	    $crawler = new Crawler($url_list, $data_directory);
	    if(!$crawler->crawl())
	    {
		    $ret_code["status"] = "fail";
		    $ret_code["fail_reason"] = "unable to write to file";
	    }
	    return $ret_code;
    }
}
?>
