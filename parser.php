<!doctype html>
	<html>
		<head>
			<title>
				AWS Transcript JSON to Blog Format
			</title>
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
			<script type="text/javascript" src="jquery.inlineEdit.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
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

		    //$translateResponse = json_decode(do_curl_request($transcribeURL)); //AWSTranscribe JSON stored in associative array
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
							$segments = "";

							$i = 0;
							$currentSpeaker = "";
							$textString = "";
							foreach($value['speaker_labels']['segments'] as $segment => $segVal) {
								if($segVal['speaker_label'] != $currentSpeaker) {
									$segments .= $segVal['speaker_label'].", ".$segVal['start_time'].", ".$segVal['end_time']." || ";
									$currentSpeaker = $segVal['speaker_label'];
									$textString .= "<p> Speaker Change @ ".$segVal['start_time']."s: ".$segVal['speaker_label']."</p><p>";									
								}
								do {
									if($value['items'][$i]['type'] == "pronunciation")
										$textString .= " ";
									//Fix capitalization of "I"
									$currWord = $value['items'][$i]['alternatives'][0]['content'];
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
									$textString .= $currWord;
								}
								while($value['items'][$i++]['type'] == "pronunciation");
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
  				<div id="AWSTranscribeSpeakers">Raw Speakers: <?php echo $segments?></div>
  				<div id="content">
  					<div id="AWSTranscribeTextItems">Raw Text Items: <?php echo $textString?></div>
  				</div>
			</div>
			<BR>
		</body>
		<!--previous location of main script-->
		<script type="text/javascript">
			$(function() {
			  $('.editable').inlineEdit({
			    control: 'textarea',
			  });
			});
		</script>
		<button onclick="copyToClipboard('AWSTranscribeTextItems'); alert('COPIED')">Copy AWSTranscribeTextItems</button>

<!--- see solution from Alvaro Montoro on https://stackoverflow.com/questions/22581345/click-button-copy-to-clipboard-using-jquery --->
		<script>
			function copyToClipboard(elementId) {
			  // Create a "hidden" input
			  var aux = document.createElement("input");
			  //TODO: Find and replace any of the spans used for the inline formatter
			  var tmpText = document.getElementById(elementId).innerHTML
			  
			  // Assign it the value of the specified element
			  aux.setAttribute("value", tmpText);
			  // Append it to the body
			  document.body.appendChild(aux);
			  
			  // Highlight its content
			  aux.select();
			  // Copy the highlighted text
			  document.execCommand("copy");
			  // Remove it from the body
			  document.body.removeChild(aux);
			}
		</script>
	</html>