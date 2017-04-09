<?php

class GoogleCalendar
{
//    private $month;
//    private $day;
//    private $year;
    
    public function __construct($API_URL)
    {
        $this->url = $API_URL;
    }
    
//    public function setDate($month, $day, $year)
//    {
//        $this->month = $month;
//        $this->day = $day;
//        $this->year = $year;
//    }
    
    public function makeURL()
    {
        $month = date("n");
        $day = date("j");
        $year = date("Y");
        
        $this->t = mktime(0, 0, 0, $month, $day, $year);
        $day++;
        $this->t2 = mktime(0, 0, 0, $month, $day, $year);
        
        $this->params = array();
        $this->params[] = 'orderBy=startTime';
        //最大表示件数 指定しなければ全て
//        $this->params[] = 'maxResults=10';
        //ここから
        $this->params[] = 'timeMin='.urlencode(date('c', $this->t));
        //ここまでのカレンダーを取得
        $this->params[] = 'timeMax='.urlencode(date('c', $this->t2));
        
        $this->url .= '&'.implode('&', $this->params);
        
        return $this->url;
    }
    
    public function getSummary()
    {
        $this->results = file_get_contents(self::makeURL());
        $this->json = json_decode($this->results, true);
        
        $this->summaries = array();
        $no = 0;
        $last = count($this->json["items"]) - 1;
        foreach($this->json["items"] as $eachPlan) {
            if($no !== last) {
                $this->summaries[] = $eachPlan["summary"] . "\n";
            } else {
                $this->summaries[] = $eachPlan["summary"];
            }
            $no++;
        }
        
        foreach($this->summaries as $summary) {
            $this->reply .= $summary;
        }
        
        return $this->reply;
    }
    
}
