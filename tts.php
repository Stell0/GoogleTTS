<?php

if (!isset($argv[1]) || ! file_exists($argv[1])) {
	fwrite(STDERR,"Usage: $argv[0] <TEXT FILE>\n");
	exit (1);
}
//Include Speech To Text Google libraries
require_once 'vendor/autoload.php';
// Include Speech to Text libraies
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\SpeechContext;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
// Include Text to Speech libraries
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding as TTSAudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

$lines = explode("\n",file_get_contents($argv[1]));
$i = 0;
$text = [];
$text[0] = '';
foreach ($lines as $line) {
	if ( strlen($line) === 0 ) continue;
	if ( (strlen($text[$i]) + strlen($line)) > 5000 ) {
		$i++;
		$text[$i] = '';
	}
	$text[$i] .=  $line;
}

foreach ($text as $k => $t) {
	$output_file = $k.'.mp3';
	$client = new TextToSpeechClient(['credentials' => 'google-auth.json']);
	$input_text = (new SynthesisInput())
		->setText($t);
	/* available voices https://cloud.google.com/text-to-speech/docs/voices */
	/* English male voice */
	$voice = (new VoiceSelectionParams())
		->setLanguageCode('en')
		->setName('en-US-Wavenet-B')
		->setSsmlGender(SsmlVoiceGender::MALE);
	/* Italian male voice */
	//$voice = (new VoiceSelectionParams())
	//	->setLanguageCode('it')
	//	->setSsmlGender(SsmlVoiceGender::MALE);

	$audioConfig = (new AudioConfig())
		->setAudioEncoding(TTSAudioEncoding::MP3);
	$response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);
	$audioContent = $response->getAudioContent();
	file_put_contents($output_file, $audioContent);
	$client->close();
}

$cmd = "sox ";
foreach ($text as $k => $t) {
	$cmd .= $k.".mp3 ";
}
$cmd .= $argv[1].".mp3";
exec($cmd);
foreach ($text as $k => $t) {
	unlink($k.'.mp3');
}


