<?php
    
    $serverName = "localhost";
    $username ="root";
    $password = "";
    $db = "test"; 

    try {
        $conn = new PDO("mysql:host=$serverName;dbname=$db", $username, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        //set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";

        $conn->beginTransaction();
        $stmt = $conn->prepare("SELECT * FROM tbl_article;");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        $stmt->closeCursor();

        /*header('Content-type: text/html; charset=utf-8');    
        print_r($result);*/
        //var_dump($result);

        /*header('Content-type: text/html; charset=utf-8');
        $i = 0;
        foreach ($result as $row) {
            echo ++$i;
            echo $row['article_title']."<br/>";
            echo "str length:".mb_strlen($row['article_title'])."<br/>";
            

        }*/

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        //echo json_encode($result);

        $conn->commit();


    } catch (PDOException $pdoe) {
        $conn->rollBack();
        echo "Connection failed: ". $pdoe->getMessage();
    }

    
    

