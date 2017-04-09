<?php

class GoogleCalendar
{
    private $params = array();
    private $url;
    private $t;
    private $t2;
    
    public function __construct($API_URL)
    {
        $this->url = $API_URL;
    }
    
    public function makeURL($month, $date, $year)
    {
        $this->t = mktime(0, 0, 0, $month, $date, $year);
        $date++;
        $this->t2 = mktime(0, 0, 0, $month, $date, $year);
        
        $this->params[] = 'orderBy=startTime';
        $this->params[] = 'maxResults=10';
        $this->params[] = 'timeMin='.urlencode(date('c', $this->t));
        $this->params[] = 'timeMax='.urlencode(date('c', $this->t2));
 
//        $this->url = $API_URL.'&'.implode('&', $this->params);
        $this->url .= '&'.implode('&', $this->params);
        
        return $this->url;
    }   
}
