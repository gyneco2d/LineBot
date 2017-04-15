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
                    //予定コマンド
                    if(preg_match('/^\/予定*/', $message['text'])) {
                        //日時指定なしは当日の予定
                        if(preg_match('/^\/予定$/', $message['text'])) {
                            $reply = $calendar->getSummary(date("n"), date("j"), date("Y"));
                        }
                        //予定コマンドと引数間のスペースは[\s　]で全角スペースも対応
                        else if(preg_match('/^\/予定[\s　]*/', $message['text'])) {
                            //指定日の書式は[月/日/年]の形
                            if(preg_match('/^\/予定[\s　]+[0-9]+\/[0-9]+\/[0-9]{4}/', $message['text'])) {
                                //空白文字を境に文字列を分割する
                                $matches = preg_split('/[\s　]+/', $message['text']);
                                $data = preg_split('/[\/]+/', $matches[1]);
                                
                                $reply = $calendar->getSummary($data[0], $data[1], $data[2]);
                            }
                            else {
                                $reply = '引数の書式が無効です';
                            }
                        }
                        else {
                            $reply = '存在しないコマンド. またはコマンドと引数間に空白がありません.';
                        }
                    }
                    else {  //bot生存確認用
                        $reply = '---';
                    }
                    
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                'text' => $reply
                            )
                        )
                    ));
                    
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