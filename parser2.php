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

							foreach($value['speaker_labels']['segments'] as $segment => $segVal) {
								if($segVal['speaker_label']) {
								}
							}
							/*foreach($value['items'] as $itemKey => $itemValue) {
									if($itemValue['type'] == "pronunciation") {
										$textString .= " ";
										$itemStart = $itemValue['start_time'];
										$itemEnd = $itemValue['end_time'];
										$segStart = $segVal['start_time'];
										$segEnd = $segVal['end_time'];
										if($segStart <= $itemStart && $segEnd >= $itemEnd) {
											$currWord = $itemValue['alternatives'][0]['content'];
											$textString .= $currWord;
										}
									}
									else {
										$textString .= $itemValue['alternatives'][0]['content'];
									}
								}*/

								/*if($segVal['speaker_label'] != $currentSpeaker) {
									$currentSpeaker = $segVal['speaker_label'];
									$speakersArray[$i++] = $segVal['speaker_label']." ".$segVal['start_time']." ".$segVal['end_time'];
									$textFound = 0;
									$textItems = array(); //Doesn't appear to be used
									$j = 0;
									$textString .= "<p> Speaker Change @ ".$segVal['start_time']."s: ".$segVal['speaker_label']."</p><p>";
								}	

									/*foreach($value['items'] as $itemKey => $itemValue) {
										if($itemValue['type'] == "pronunciation") {
											$itemStart = $itemValue['start_time'];
											$itemEnd = $itemValue['end_time'];
											$segStart = $segVal['start_time'];
											$segEnd = $segVal['end_time'];
											if($segStart <= $itemStart && $segEnd >= $itemEnd) {
											echo("<p>segStart: ".$segStart."</p>");
											echo("<p>itemStart: ".$itemStart."</p>");
											echo("<p>SegEnd: ".$segEnd."</p>");
											echo("<p>itemEnd: ".$itemEnd."</p>");
											echo "<p>_______________________________</p>";
											echo "<p>Start: ".($itemStart - $segStart)."</p>";
											echo "<p>End: ".($segEnd - $itemEnd)."</p>";//
												$textFound++;												
												//Fix capitalization of "I"
												$currWord = $itemValue['alternatives'][0]['content'];
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
										}

										//Add spaces between words, but not before punctuation.
										/*$wordBoundary = "";
										switch($itemValue['type']) {
											case "pronunciation":
												$wordBoundary = " ";
												break;
											case "punctuation":
												$wordBoundary = "";
												break;
											default:
											$wordBoundary = "";
										}
									}*/

									/*$textItems[$j++] = $currWord.$wordBoundary;
									$currWord = str_replace("\"","", $currWord); //Remove double quotes
									$textString .= $wordBoundary.$currWord;
								}
								$textString .= "<p>";*/

							break;
						default:
							echo "ERROR: Invalid JSON format: ".$key;
							break;
				}
		    } 
		    echo $textFound;
		    echo $textString;
		?>
			<div class="container">
  				<div id="AWSTranscribeJobname">JobName: <?php echo $jobName?></div>
  				<div id="AWSTranscribejobStatus">Job Status: <?php echo $status?></div>
  				<div id="AWSTranscribeaccountId">Account ID: <?php echo $accountId?></div>
  				<div id="AWSTranscribenumberOfSpeakers">Number of Speakers: <?php echo $numSpeakers?></div>
  				<div id="AWSTranscribenumberOfSpeakersNames">Name of Speakers:</div>
  				<div id="AWSTranscriberesults">Raw Results: <?php if(isset($transcribeResults)) {echo $transcribeResults;}?></div>
  				<div id="AWSTranscribeSpeakers">Raw Speakers: <?php print_r($speakersArray)?></div>
  				<div id="content">
  					<div id="AWSTranscribeTextItems">Raw Text Items: <?php if($textFound > 0) {echo $textString;}?></div>
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