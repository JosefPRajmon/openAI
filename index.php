<?php
include_once("openAiObject.php");

?>
<?php
/*
session_start();
//pod jmenem chatbota 
$chatBotID ="asst_qwNsFaZ9AQVNHOKNqC8wRDoz";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['array'])) {
        $_SESSION['array'] = [];
    }
    else{
        $array = $_SESSION["array"];
    }
    if (isset($array)) {
        $array = $test->continue($array,$chatBotID);
    }else{

        $array = $test->continue(array(),$chatBotID);
        $_SESSION["array"] = $array;
    }
    
}
*/
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css.css">
    <title>Jednoduchý chat</title>
</head>

<body>
    <div class="chat"> 
        <div class="messages">       
        <?php
        if (isset($_POST['message'])) {
            if (strlen($_POST['message'])>1) {
                $sendMessage = $_POST["message"];
                //$test->AddInstruction($sendMessage,isset($_POST["table"]));

                //$test->NewMessage($sendMessage);
                //$a= $test->CheckReply();
                //var_dump($a[0]->content[0]->text);
                    //var_dump($a);
                    $a=array(
                        new Message("zprava jedna","user"),
                        new Message("zprava dva","assistent"),
                        new Message("zprava tři","user"),
                        new Message("zprava čtiři","assistent"),
                        new Message("zprava pět","user"),
                        new Message("zprava sest","assistent"),
                        new Message("zprava sedm","user"),
                        new Message("zprava osm","assistent"),
                        new Message("zprava devet","user"),
                        new Message("zprava deset","assistent")
                    );
                foreach (array_reverse($a) as $key => $value) {
                    $role = $value->role;
                    //$message =$value->content[0]->text->value;
                    //$message = $test->ReplaceInResponse($message,$role);
                    // $message=preg_replace('/\[(https:\/\/.*?)]\((https:\/\/.*?)\)/i', '<a href="\1">\2</a>', $message);

                    $message =$value->message;
                    echo("<p class=\"$role\"><strong> $role<br></strong> $message</p>");
                }
                
            }        
        }
        ?>
        </div>
      <form method="post">
       
        
        <textarea class="message" name="message" cols="23" rows="1"></textarea>
        <input type="checkbox" name="table" class="checkbox" title="do tabulky">
        <input type="submit" class="send" value="Odeslat">
        </form>


    </div>
  
    <script src="chat.js"></script>
    <script>
      /*  var userId = 'x'; // Nahraďte 'x' ID uživatele, kterého chcete sledovat

setInterval(function() {
    $.ajax({
        url: 'get_messages.php',
        data: { 'user_id': userId }, // Přidejte ID uživatele jako parametr požadavku
        success: function(data) {
            $('.messages').html(data);

            var messagesDiv = document.querySelector('.messages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    });
}, 1000);
*/
    </script>
</body>
</html>

