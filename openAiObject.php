<?php
include("config.php");
//$test =  new openAiObject($open_Api_Key);

class openAiObject{
    private $OPENAI_API_KEY;
    private $THREAD_ID;
    private $RUN_ID;
   /*
    function __destruct(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $this->THREAD_ID . '/runs/' . $this->RUN_ID . '/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->OPENAI_API_KEY,
            'OpenAI-Beta: assistants=v1'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }*/
   public function Continue($array,$ai_id){
    if(count($array)>0){
        $this->THREAD_ID = $array[0];
        $this->RUN_ID = $array[1];
    }else{
        $this->CreateThread();
        $this->StartRuns($ai_id);
        return array($this->THREAD_ID,$this->RUN_ID);
    }
   }
   public function __construct($id){
    $this->OPENAI_API_KEY = $id;
   }
    private function CreateThread(){
        $curl = curl_init();
        /*
            $data = array(
            "messages" => array(
                array(
                    "role" => "user",
                    "content" => "ahoj, od kolika hodin je otevřena staroměstká radnice?"
                )
            )
        );*/

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.openai.com/v1/threads',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        //CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '. $this->OPENAI_API_KEY,
            'OpenAI-Beta: assistants=v1'
        ),
        ));

        $response = json_decode(curl_exec($curl));
        $this->THREAD_ID= $response->id;
    }

    public function StartRuns($ai_id){
        $curl = curl_init();
        $data = array(
            "assistant_id" => $ai_id
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $this->THREAD_ID . '/runs',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->OPENAI_API_KEY,
            'OpenAI-Beta: assistants=v1'
        ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $this->RUN_ID = $response->id;
    }
    function CheckReply(){
        //set_time_limit(1200);
        do{
            
            $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $this->THREAD_ID . '/runs/' . $this->RUN_ID,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->OPENAI_API_KEY,
            'OpenAI-Beta: assistants=v1'
          ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        sleep(10);

        }while (json_decode($response)->status != "completed");

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $this->THREAD_ID . '/messages',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->OPENAI_API_KEY,
            'OpenAI-Beta: assistants=v1',
            'limit: 100'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response)->data;
    }
    function ReplaceInResponse($response,$role){
        //odstranění zdroje
        $pattern = "/【.*?】/";
        $replacement = "";
        $response = preg_replace($pattern, $replacement, $response);
        //var_dump($response);
        $response = preg_replace('/\[(https:\/\/.*?)]\((https:\/\/.*?)\)/i', '<a href="\1">\2</a>', $response);
        //   var_dump(json_decode($link));
        if ($role=="user") {
            $position = strpos($response, "instruction");
            $response = substr($response, 0, $position);
        }

        return $response;
    }
    function AddInstruction($sendMessage,$table){
        $sendMessage = $sendMessage . "instruction";
        if($table){
            $sendMessage = $sendMessage . " tabulky vzdy tvoř pomocí html, do tabulky vzdy dej classu pro řádek a tabulku samotnou";
        }
        return $sendMessage;
    }
    function NewMessage($message){
        $url = "https://api.openai.com/v1/threads/$this->THREAD_ID/messages";

        $data = array(
            "role" => "user",
            "content" => "$message"
        );
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->OPENAI_API_KEY",
            "OpenAI-Beta: assistants=v1"
        ));

        $response = curl_exec($ch);

        if($response === false)
            echo 'Chyba: ' . curl_error($ch);

        curl_close($ch);

    }
}
class Message
{
    public $message;
    public $role;
    function __construct($message,$user){
        $this->role = $user;
        $this->message =$message;
    }
}


//uplaud a mazání souboru ŕozdeláno
$test = new FilesManagment($open_Api_Key);
//ve funkci FileUplaud je třeba upravit cestu k souboru $file = new CURLFile(__DIR__ . '/' . $fileName, 'application/octet-stream',$fileName);
//funkce se postará o smazaní puvodních souboru a nahraní +napojení nových
$test->DataFunctionCaller("asst_qwNsFaZ9AQVNHOKNqC8wRDoz","prostejov-novinky.jsonl");

class FilesManagment{
    private $OPENAI_API_KEY;
    public function __construct($id){
        $this->OPENAI_API_KEY = $id;
    }

    function DataFunctionCaller($assistID,$fileName){
        //najití a smazání souboru se stejným jménem pokud není tak to chytně try catch
        $arrayAllFiles =json_decode($this->GetFiles())->data;
            foreach ($arrayAllFiles as $key => $value) {
                if ($value->filename == $fileName) {
                    try {
                        $this->RemoveFilesFromAssist($assistID,$value->id);
                    } catch (Throwable $th) {
                        //throw $th;
                    }
                    try {
                        $this->DeleteFile($assistID,$value->id);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

        $this->FileUplaud($assistID,$fileName);

    }

    function GetFiles(){
        $url = "https://api.openai.com/v1/files";

        $headers = array(
            "Authorization: Bearer " . $this->OPENAI_API_KEY
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return $result;

    }

    function GetAssistFiles(){
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->OPENAI_API_KEY,
            "OpenAI-Beta: assistants=v1"
        );

        $ch = curl_init( "https://api.openai.com/v1/assistants?order=desc&limit=20");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        var_dump($result);
    }

    function FileUplaud($assistID,$fileName){
        $ch = curl_init();
        $file = new CURLFile(__DIR__ . '/' . $fileName, 'application/octet-stream',$fileName);
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/files');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'purpose' => 'assistants',
            'file' => $file
        ));
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $headers = array();
        $headers[] = 'User-Agent: Apidog/1.0.0 (https://apidog.com)';
        $headers[] = 'Authorization: Bearer ' . $this->OPENAI_API_KEY;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump(curl_errno($ch));
            var_dump( 'Error:' . curl_error($ch));
        }
        $this->CreateAssistFile($assistID,json_decode($result)->id);
    }

    function CreateAssistFile($assistID,$fileID){
        $url = "https://api.openai.com/v1/assistants/$assistID/files";
        
        $headers = array(
            "Authorization: Bearer " . $this->OPENAI_API_KEY,
            "Content-Type: application/json",
            "OpenAI-Beta: assistants=v1"
        );
        
        $data = array(
            "file_id" => $fileID
        );
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        curl_close($ch);
        
        //var_dump($result);
    }

    function DeleteFile($assistID,$fileID){
        $this->RemoveFilesFromAssist($assistID,$fileID);
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/files/$fileID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $this->OPENAI_API_KEY;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
    }

    function RemoveFilesFromAssist($assist,$file){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/assistants/$assist/files/$file");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $this->OPENAI_API_KEY;
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'OpenAI-Beta: assistants=v1';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

    }
}?>