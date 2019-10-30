<?php


$API_URL = 'https://api.line.me/v2/bot/message';
$ACCESS_TOKEN = 'J0fDzIgCNNgs8vjTSFf8BxfOSxsPV7vM2Al0H8DkS4kao5gAwHmeYs6GcYUWb265vPX/Ypk5dxhrIeQkJSf2UNTWeBJlWoZVlDHH1u0IsYSpMKzVdQs6RFcHjZzpjPsScMIsL1kiqxuGchbtfjNW7wdB04t89/1O/w1cDnyilFU='; 
$channelSecret = '675cfd3ef9f514c8b5e541a085454097';


$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array



if ( sizeof($request_array['events']) > 0 ) {

    foreach ($request_array['events'] as $event) {

        $reply_message = '';
        $id = '';
        if(isset($event['source']['userId']){
            $id = $event['source']['userId'];
         }
         else if(isset($event['source']['groupId'])){
            $id = $event['source']['groupId'];
         }
         else if(isset($event['source']['room'])){
            $id = $event['source']['room'];
         }
       

        $text = $event['message']['text'];
        $arrayPostData = array();
        $reply_token = $event['replyToken'];
        $arrayPostData['replyToken'] = $reply_token;

        if($text == "pic"){
            $image_url = "https://i.pinimg.com/originals/cc/22/d1/cc22d10d9096e70fe3dbe3be2630182b.jpg";
            $arrayPostData['messages'][0]['type'] = "image";
            $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
            $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
        } else if($text == "ราคาน้ำมัน"){
            $client = new SoapClient("http://www.pttplc.com/webservice/pttinfo.asmx?WSDL", // URL ของ webservice
		    	array(
			           "trace"      => 1,		// enable trace to view what is happening
			           "exceptions" => 0,		// disable exceptions
			          "cache_wsdl" => 0) 		// disable any caching on the wsdl, encase you alter the wsdl server
                   );
                   
                   // ตัวแปลที่ webservice ต้องการสำหรับ GetOilPriceResult เป็นวันเดือนปีและ ภาษา  
            $params = array(
                'Language' => "en",
                'DD' => date('d'),
                'MM' => date('m'),
                'YYYY' => date('Y')
            );

           // เรียกใช้ method GetOilPrice และ ใส่ตัวแปลเข้าไป 
           $data = $client->GetOilPrice($params);
           
           //เก็บตัวแปลผลลัพธ์ที่ได้
           $ob = $data->GetOilPriceResult;
           
          // เนื่องจากข้อมูลที่ได้เป็น string(ในรูปแบบ xml) จึงต้องแปลงเป็น object ให้ง่ายต่อการเข้าถึง
           $xml = new SimpleXMLElement($ob);
        
          // attr  PRICE_DATE , PRODUCT ,PRICE
         //loop เพื่อแสดงผล  
         $txt = '';
         foreach ($xml  as  $key =>$val) {  
         
           // ถ้าไม่มีราคาก็ไม่ต้องแสดงผล เนื่องจากมีบางรายการไม่มีราคา   
           if($val->PRICE != ''){
                $txt = $txt.$val->PRODUCT.'  '.$val->PRICE;
            }

          }

            $arrayPostData['messages'][0]['type'] = "text";
            $arrayPostData['messages'][0]['text'] = $txt;
        }else{
            $arrayPostData['messages'][0]['type'] = "text";

            //$get_data = callAPI('GET', 'https://mos.modernform.co.th/mos-client/item?site=MF&item=5-M-COS-AC-CM120-BK', false);
            //$response = json_decode($get_data, true);
            //$errors = $response['response']['errors'];
            //$data = $response['response']['data'][0];
           // $url = 'https://dev.modernform.co.th/qn/rs/api/';
            //$json = file_get_contents($url);
           // $jsondata =  json_decode($json, true);
           // $txt = '';
           // foreach($jsondata as $value){
            //    $txt = $value['description'];
            //}

            $arrayPostData['messages'][0]['text'] = $text;

            $arrayPostData1['to'] = $id;
            $arrayPostData1['messages'][0]['type'] = "text";
            $arrayPostData1['messages'][0]['text'] = "สวัสดีจ้าาา";
            $arrayPostData1['messages'][1]['type'] = "sticker";
            $arrayPostData1['messages'][1]['packageId'] = "2";
            $arrayPostData1['messages'][1]['stickerId'] = "34";
            pushMsg($POST_HEADER,$arrayPostData1);
        }
        $post_body = json_encode($arrayPostData, JSON_UNESCAPED_UNICODE);

        $send_result = send_reply_message($API_URL.'/reply', $POST_HEADER, $post_body);

        echo "Result: ".$send_result."\r\n";
    }
}

echo "OK";

function pushMsg($arrayHeader,$arrayPostData){
    $strUrl = "https://api.line.me/v2/bot/message/push";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$strUrl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close ($ch);
 }


function send_reply_message($url, $post_header, $post_body)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function callAPI($method, $url, $data){
    $curl = curl_init();
 
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
 
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'APIKEY: 111111111111111111111',
       'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
 
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 }

?>