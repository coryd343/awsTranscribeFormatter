<!doctype html>
	<html>
		<head>
			<title>
				AWS Transcript JSON to Blog Format
			</title>
			<style>
			textarea{
			width:100%;
			height:100%;
			}
			</style>
		</head>
		<body>
		<?php
			//Janky biz necessary only while reading data from a static JSON file
			$translateAlHughes = json_decode(file_get_contents("al-hughes.json"), true);

			$transcribeURL = "http://www.notactualurl.com"; //We want this source to be dynamic. Probably an env. variable
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

		    function readSegmentArray($segArray) {
		    	for($i = 0; $i < count($segArray); $i++) {
		    		echo $segArray[$i][0].", ".$segArray[$i][1].", ".$segArray[$i][2]." || ";
		    	}
		    }

		    //$translateResponse = json_decode(do_curl_request($transcribeURL));
		    foreach($translateAlHughes as $key => $value) {
		    	switch($key) {
						case "jobName":
							$jobName = $value;
							break;
						case "accountId":
							$accountId = $value;
							break;
						case "status":
							$status = $value;
							break;
						case "results":
							$transcribeResults = $value['transcripts'][0]['transcript'];
							$numSpeakers = $value['speaker_labels']['speakers'];
							$segments = array();

							$segNum = 0;
							$i = 0;
							$j = 0;
							$numWords = count($value['items']);
							$currentSpeaker = $value['speaker_labels']['segments'][0]['speaker_label'];
							$segStart = $value['speaker_labels']['segments'][0]['start_time'];
							$segEnd = $value['speaker_labels']['segments'][0]['end_time'];
							$textString = "<p> Speaker Change @ ".$segStart."s: ".$currentSpeaker."</p><p>";

							//Main text processing loop
							foreach($value['speaker_labels']['segments'] as $segment => $segVal) {
								if($segVal['speaker_label'] == $currentSpeaker) {
									$segEnd = $segVal['end_time'];
								}
								else {
									$segments[$j++] = array(
										$currentSpeaker,
										$segStart,
										$segEnd
									);
									$segStart = $segVal['start_time'];
									$segEnd = $segVal['end_time'];
									$currentSpeaker = $segVal['speaker_label'];
									$textString .= "<p> Speaker Change @ ".$segVal['start_time']."s: ".$segVal['speaker_label']."</p><p>";	
								}
								if($segNum == count($value['speaker_labels']['segments']) - 1) {
									echo "<p>Last segment: ".$segNum."</p>";
									$segments[$j++] = array(
										$currentSpeaker,
										$segStart,
										$segEnd
									);
								}

								do {
									$currWord = $value['items'][$i]['alternatives'][0]['content'];
									if($value['items'][$i]['type'] == "pronunciation") {
										$textString .= " ";
										//Fix capitalization of "I"
										switch($currWord) {
											case "i":
												$currWord = "I";
												break;
											case "i'm":
												$currWord = "I'm";
												break;
											case "i'd":
												$currWord = "I'd";
												break;
											case "i've":
												$currWord = "I've";
												break;
											default:
										}
									}									
									$textString .= $currWord;
									$i++;
								}
								while($i < $numWords && ($value['items'][$i]['type'] == "punctuation" || ($value['items'][$i]['start_time'] >= $segVal['start_time'] && $value['items'][$i]['end_time'] <= $segVal['end_time'])));
								$segNum++;
							}
							$textString .= "</p>";
							break;
						default:
							echo "ERROR: Invalid JSON format: ".$key;
							break;
				}
		    }
		?>
			<div class="container">
  				<div id="AWSTranscribeJobname">JobName: <?php echo $jobName?></div>
  				<div id="AWSTranscribejobStatus">Job Status: <?php echo $status?></div>
  				<div id="AWSTranscribeaccountId">Account ID: <?php echo $accountId?></div>
  				<div id="AWSTranscribenumberOfSpeakers">Number of Speakers: <?php echo $numSpeakers?></div>
  				<div id="AWSTranscribenumberOfSpeakersNames">Name of Speakers:</div>
  				<div id="AWSTranscriberesults">Raw Results: <?php if(isset($transcribeResults)) {echo $transcribeResults;}?></div>
  				<div id="AWSTranscribeSpeakers">Raw Speakers: <?php readSegmentArray($segments)?></div>
  				<div id="content">
  					<div id="AWSTranscribeTextItems">Raw Text Items: <?php echo $textString?></div>
  				</div>
			</div>
			<BR>
		</body>
	</html>