<?php
	$transcribeURL = "http://www.notactualurl.com"; //We want this source to be dynamic. Probably an env. variable
	$destinationURL = "http://www.notactualurl.com";
	$source = new DOMdocument();
    $source->loadHTMLFile($destinationURL);
    $path = new DOMXpath($source);
    //$dom = $path->query("*/div[@id='test']"); //Example of how to reference DOM object

	//$speakerName = array('spk_0' => 'Narrator', 'spk_1' => 'Pastor Hughes');
	function do_curl_request($url, $params = array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        //determine whether GET or POST by #of args
        if(is_array($params) && count($params)) {
            curl_setopt($curl, CURLOPT_POST, count($params));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    $translateResponse = json_decode(do_curl_request($transcribeURL)); //AWSTranscribe JSON stored in associative array
    foreach($translateResponse as $key => $value) {
    	switch(key) {
				case "jobName":
					//$( "<span>"+val+"</span>" ).appendTo( "#AWSTranscribeJobname");

					break;
				case "accountId":
					//$( "<span>"+val+"</span>" ).appendTo( "#AWSTranscribeaccountId");
					break;
				case "status":
					//$( "<span>"+val+"</span>" ).appendTo( "#AWSTranscribejobStatus");
					break;
				case "results":
					//lotsa stuff
					break;
				default:
					break;
		}
    }

?>