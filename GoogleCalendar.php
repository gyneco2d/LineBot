<?php

class GoogleCalendar
{

    public function __construct($API_URL)
    {
        $this->url = $API_URL;
    }
    
    public function setDate()
    {
        $this->start = mktime(0, 0, 0, $this->month, $this->day, $this->year);
        $this->day++;
        $this->last = mktime(0, 0, 0, $this->month, $this->day, $this->year);
    }
    
    public function makeURL()
    {
        self::setDate();
        
        //取得する情報を決めるURLを作る
        $this->params = array();
        $this->params[] = 'orderBy=startTime';
        //最大表示件数 指定しなければ全て
//        $this->params[] = 'maxResults=10';
        //ここから
        $this->params[] = 'timeMin='.urlencode(date('c', $this->start));
        //ここまでのカレンダーを取得
        $this->params[] = 'timeMax='.urlencode(date('c', $this->last));
        
        $this->url .= '&'.implode('&', $this->params);
        
        return $this->url;
    }
    
    public function getSummary($month, $day, $year)
    {
        $this->month = $month;
        $this->day = $day;
        $this->year = $year;
        
        $this->results = file_get_contents(self::makeURL());
        $this->json = json_decode($this->results, true);
        
        //予定の概要を取ってくる
        $this->summaries = array();
        $no = 0;
        $last = count($this->json["items"]) - 1;
        foreach($this->json["items"] as $eachPlan) {
            if($no !== last) {
                $this->summaries[] = $eachPlan["summary"] . "\n";
            } else {    //最後は改行しない
                $this->summaries[] = $eachPlan["summary"];
            }
            $no++;
        }
        
        //LINEに返す形に整理
        foreach($this->summaries as $summary) {
            $this->reply .= $summary;
        }
        
        if($this->reply == '') {
            $this->reply = '予定はありません';
        }
        
        return $this->reply;
    }
    
}
