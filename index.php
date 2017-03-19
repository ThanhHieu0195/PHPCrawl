<?php

set_time_limit(10000);
$INDEX = 1;
$MAX_URL = 5000;
$LINK_URL_HTML = '';
include("libs/PHPCrawler.class.php");
include_once 'simple_html_dom.php';

class MyCrawler extends PHPCrawler
{
  function handleDocumentInfo($DocInfo) 
  {
    global $INDEX, $LINK_URL_HTML;
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    echo "Referer-page: ".$DocInfo->referer_url.$lb;

    if ($DocInfo->received == true){
        echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;

        $html = file_get_html($DocInfo->url);
        $name_file_html = 'html/'.$INDEX.'.html';
        $html->save($name_file_html);
        $row_html = $INDEX.'. '.$DocInfo->url."\n";
        $INDEX++;
        $LINK_URL_HTML .= $row_html;
    }
    else
      echo "Content not received".$lb; 
    

    echo $lb;
    
    flush();
  } 
}

$crawler = new MyCrawler();

$crawler->setURL("http://vnexpress.net/");

$crawler->addContentTypeReceiveRule("#text/html#");

$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

$crawler->enableCookieHandling(true);

//$crawler->setTrafficLimit(10000 * 1024);

$crawler->go();

$report = $crawler->getProcessReport();

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb;

$myfile = fopen("html/downloadList.txt", "w") or die("Unable to open file!");
$txt = $LINK_URL_HTML;
fwrite($myfile, $txt);
fclose($myfile);
?>