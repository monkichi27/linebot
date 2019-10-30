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
        $reply_token = $event['replyToken'];

        $text = $event['message']['text'];
        $arrayPostData = array();
        $arrayPostData['replyToken'] = $reply_token;

        if($text == "pic"){
            $image_url = "https://i.pinimg.com/originals/cc/22/d1/cc22d10d9096e70fe3dbe3be2630182b.jpg";
            $arrayPostData['messages'][0]['type'] = "image";
            $arrayPostData['messages'][0]['originalContentUrl'] = $image_url;
            $arrayPostData['messages'][0]['previewImageUrl'] = $image_url;
        }else{
            $arrayPostData['messages'][0]['type'] = "text";

            //$get_data = callAPI('GET', 'https://mos.modernform.co.th/mos-client/item?site=MF&item=5-M-COS-AC-CM120-BK', false);
            //$response = json_decode($get_data, true);
            //$errors = $response['response']['errors'];
            //$data = $response['response']['data'][0];
            $url = 'https://mos.modernform.co.th/mos-client/item?site=MF&item=5-M-COS-AC-CM120-BK';
            $json = file_get_contents($url);
            $jsondata =  json_decode($json, true);
            $txt = '';
            foreach($jsondata as $value){
                $txt = $value['description'];
            }

            $arrayPostData['messages'][0]['text'] = $txt;
        }
        $post_body = json_encode($arrayPostData, JSON_UNESCAPED_UNICODE);

        $send_result = send_reply_message($API_URL.'/reply', $POST_HEADER, $post_body);

        echo "Result: ".$send_result."\r\n";
    }
}

echo "OK";




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