<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');
require_once('./setting.php');
require_once('./GoogleCalendar.php');

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
$calendar = new GoogleCalendar($API_URL);

foreach($client->parseEvents() as $event) {
    switch($event['type']) {
        case 'message':
            $message = $event['message'];
            switch($message['type']) {
                case 'text':
                    //strcmp 文字列の比較
                    if(strcmp($message['text'], '今日の予定') == 0) {
                        
                        $url = $calendar->makeURL(date("n"), date("j"), date("Y"));
                        $results = file_get_contents($url);
                        //json文字列をphp変数に変換する
                        $json = json_decode($results, true);
 
                        //予定のタイトル
                        for($i=0; $i<10; $i++) {
                            $titles[] = $json["items"][$i]["summary"];
                        }
                        
                        $no = 0;
                        $length = count($titles);
                        foreach($titles as $title) {
                            $reply .= $title;
                            if($no++ !== $length) $reply .= "\n";
                            if($json["items"][$i]["summary"] === "") break;
                        }
 
                        $client->replyMessage(array(
                            'replyToken' => $event['replyToken'],
                            'messages' => array(
                                array(
                                    'type' => 'text',
                                    'text' => $reply,
                                )
                            )
                        ));
                    } else {
                        $client->replyMessage(array(
                            'replyToken' => $event['replyToken'],
                            'messages' => array(
                                array(
                                    'type' => 'text',
                                    'text' => '---'
                                )
                            )
                        ));
                    }
                    break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
                                                                
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};