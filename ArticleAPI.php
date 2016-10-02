<?php

class GetArticle
{
    public function __construct(){
        
        $serverName = "localhost";
        $username ="root";
        $password = "";
        $db = "test"; 

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$db", $username, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
            //set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";

            //Transaction Start
            $conn->beginTransaction();
            $stmt = $conn->prepare("SELECT * FROM tbl_article;");
            $stmt->execute();

            // set the resulting array to associative
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            $stmt->closeCursor();

            
            //JSON Production Like API
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            

            $conn->commit();


        } catch (PDOException $pdoe) {
            $conn->rollBack();
            echo "Connection failed: ". $pdoe->getMessage();
        }        
    }
}

$getArticle;

if(isset($_REQUEST['ACCESS_TOKEN']) ){
    
    if($_REQUEST['ACCESS_TOKEN']=="keyToAccess"){
        $getArticle = new GetArticle();
    }
    else{
        $msg = array(
                    array(
                        'status' => "error", 
                        'msg'   => "Cannot access API; provided invalid ACCESS_TOKEN"
                        )
            );

        header("Content-Type: application/json; charset=utf8");
        echo json_encode($msg, JSON_PRETTY_PRINT);    
    }

    
}
else{
    $msg = array(
                array(
                    'status' => "error", 
                    'msg'   => "Cannot access API; need an ACCESS_TOKEN"
                    )
            );

    header("Content-Type: application/json; charset=utf8");
    echo json_encode($msg, JSON_PRETTY_PRINT);
}

/*if(isset($_REQUEST['greeding'])){
    echo "Hello";
}*/






