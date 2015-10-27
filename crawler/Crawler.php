<?php
class Crawler
{
    private $url_list;
    private $output_directory;

    public function __construct($url_list, $output_directory)
    {
        $this->url_list = $url_list;
        $this->output_directory = $output_directory;
    }

    public function crawl()
    {
        foreach($this->url_list as $url)
        {
            $page_name = array_reverse(explode("/", $url))[0];
            $data = file_get_contents($url);

            date_default_timezone_set('America/Toronto');
            $cur_time = date('YmdHis');

            $output_file = $this->output_directory."/".$page_name."-".$cur_time;
            if(!file_put_contents($output_file, $data))
		return false;

	    return true;
        }
    }
}
?>
