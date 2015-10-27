<?php
require 'Slim/Slim.php';
require 'crawler/CrawlerDriver.php';
require 'parser/ParserDriver.php';
require 'query_processor/QueryProcessor.php';

use \Slim\Slim;

date_default_timezone_set('America/Toronto');

/* bootstrap code for slim framework */
Slim::registerAutoloader();
$app = new Slim();

/* crawl pages */
$app->get('/crawl/:incident_type', function($incident_type) 
{
	$ret_code = CrawlerDriver::start_crawling($incident_type);
	echo json_encode($ret_code);
});


/* parse pages */
$app->get('/parse/:incident_type', function($incident_type) 
{
    $ret_code = ParserDriver::start_parsing($incident_type);
    echo json_encode($ret_code);
});

$app->get('/incidents/from/:from_time(/to/:to_time)', function($from_time, $to_time = 'now') 
{
    $result_set = QueryProcessor::getDailyIncidentsDateRange($from_time, $to_time);
    echo json_encode($result_set);
});

$app->get('/incidents/summary/monthly/:month_year', function($month_year)
{
    $result_set = QueryProcessor::getDailyIncidentsMonthAggregate($month_year);
    echo json_encode($result_set);
});


$app->get('/incidents/summary(/type/:incident_type)(/from/:from_time)(/to/:to_time)', 
        function($incident_type = 'all', $from_time = 'begin', $to_time = 'now')
{
    $result_set = QueryProcessor::getIncidentsAggregate($incident_type, $from_time, $to_time);
    echo json_encode($result_set);
});

$app->get('/incidents/types', function()
{
    $result_set = QueryProcessor::getIncidentTypes();
    echo json_encode($result_set);
});

$app->get('/incidents/summary/regional(/type/:incident_type)(/from/:from_time)(/to/:to_time)', function($incident_type = 'all', $from_time = 'begin', $to_time = 'now')
{
    echo 'a';
});

$app->run();
?>
