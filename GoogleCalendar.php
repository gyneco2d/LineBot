<?php

class GoogleCalendar
{
    
    public function __construct($API_URL)
    {
        $this->url = $API_URL;
    }
    
    public function makeURL($month, $day, $year)
    {
//        $month = date("n");
//        $day = date("j");
//        $year = date("Y");
        
        $this->t = mktime(0, 0, 0, $month, $day, $year);
        $day++;
        $this->t2 = mktime(0, 0, 0, $month, $day, $year);
        
        $this->params = array();
        $this->params[] = 'orderBy=startTime';
        $this->params[] = 'maxResults=10';
        $this->params[] = 'timeMin='.urlencode(date('c', $this->t));
        $this->params[] = 'timeMax='.urlencode(date('c', $this->t2));
 
        $this->url .= '&'.implode('&', $this->params);
        
//        return $this->url;
        $this->results = file_get_contents($this->url);
        $this->json = json_decode($this->results, true);
        
        for($i=0; $i<10; $i++) {
            $this->titles[] = $this->json["items"][$i]["summary"];
        }
        
        $no = 0;
        $length = count($titles);
        foreach($this->titles as $title) {
            $this->reply .= $title;
            if($no++ !== $length) $this->reply .= "\n";
            if($this->json["items"][$i]["summary"] === "") break;
        }
        
        return $this->reply;
    }
    
//    public function getTitles()
//    {
//        $this->results = file_get_contents($this->makeURL());
//        $this->json = json_decode($this->results, true);
//        
//        for($i=0; $i<10; $i++) {
//            $this->titles[] = $this->json["items"][$i]["summary"];
//        }
//        
//        $no = 0;
//        $length = count($titles);
//        foreach($this->titles as $title) {
//            $this->reply .= $title;
//            if($no++ !== $length) $this->reply .= "\n";
//            if($this->json["items"][$i]["summary"] === "") break;
//        }
//        
//        return $this->reply;
//    }
    
}
