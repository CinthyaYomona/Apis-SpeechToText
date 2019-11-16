<?php
/*
    1.Reconocimiento síncrono (REST y gRPC):Las solicitudes de reconocimiento síncronas están limitadas a 
                                            los datos de audio de 1 minuto o menos de duración.
    2.Reconocimiento asíncrono (REST y gRPC):las solicitudes asíncronas para los datos de audio de cualquier 
                                              duración de hasta 180 minutos.
    3.Reconocimiento de transmisión continua (gRPC únicamente) realiza el reconocimiento de los datos de audio 
                                            proporcionados dentro de una transmisión continua bidireccional de gRPC.

    Este artículo describe la API de REST porque es más sencillo mostrar y explicar el uso básico de la API.
    
*/

//Hace uso de COMPOSER
require_once __DIR__ . '/../Speech/vendor/autoload.php';

# [START speech_transcribe_sync]
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

//Seteo el archivo JSON, para poder hacer uso del servicio de la API Speech to Text
putenv('GOOGLE_APPLICATION_CREDENTIALS=C:\xampp\htdocs\speech\apispeech-1573687685012-3bb1ab9d6e09.json');

/**Ruta del audio*/
$audioFile = 'C:\xampp\htdocs\speech\test\data\audio32KHz.raw';

// Configuración del AUDIO
$encoding = AudioEncoding::LINEAR16;
$sampleRateHertz = 32000;
$languageCode = 'en-US';

//obtener el contenido de un archivo en una cadena
$content = file_get_contents($audioFile);

//Setea cadena como contenido de audio
$audio = (new RecognitionAudio()) ->setContent($content);

//Setea la configuración
$config = (new RecognitionConfig())
    ->setEncoding($encoding)
    ->setSampleRateHertz($sampleRateHertz)
    ->setLanguageCode($languageCode);

//Instancia al cliente
$client = new SpeechClient();

//La variable results contiene la lista de resultados (del tipo SpeechRecognitionResult)  
try {
    $response = $client->recognize($config, $audio);
    foreach ($response->getResults() as $result) {
        $alternatives = $result->getAlternatives();
        //alternatives contiene una lista de transcripciones posibles, del tipo SpeechRecognitionAlternatives
        $mostLikely = $alternatives[0];
        $transcript = $mostLikely->getTranscript();
        $confidence = $mostLikely->getConfidence();
        $conf       = $confidence * 100;
        echo "<b>Transcripción:</b> $transcript";
        echo "<br>";
        echo "<b>Nivel de Confianza:</b> $conf %";
    }
} finally {
    $client->close();
}
# [END speech_transcribe_sync]
