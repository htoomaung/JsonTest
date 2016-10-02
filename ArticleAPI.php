<?php
class GetArticle{
    
    public static function getInstance(){
        $objInstance = new GetArticle();
        return $objInstance;
    }

    public function __construct(){}

    private function getDBConnection(){
        $serverName = "localhost";
        $username ="root";
        $password = "";
        $db = "test";
        $conn = ""; 

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$db", $username, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
            //set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
            
        } catch (PDOException $pdoe) {
            echo "Connection failed: ". $pdoe->getMessage();
        }
        return $conn;           
    }
    public function getAllArticle(){
        try {
            $conn = $this->getDBConnection();
            
            //Transaction Start
            $conn->beginTransaction();
            $stmt = $conn->prepare("SELECT * FROM tbl_article;");
            $stmt->execute();

            // set the resulting array to associative
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            $stmt->closeCursor();
            $conn->commit();
            
            //JSON Production Like API
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            
        } catch (PDOException $pdoe) {
            $conn->rollBack();
            echo "Failed to retrieve data: ". $pdoe->getMessage();
        }        
    }

    public function getLastArticle(){
        try {
            $conn = $this->getDBConnection();
            $conn->beginTransaction();
            $stmt = $conn->prepare("SELECT * FROM tbl_article ORDER BY article_id;");
            $stmt->execute();

            $result = $stmt->fetch();
            $stmt->closeCursor();
            $conn->commit();

            //JSON Production Like API
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

        } catch (PDOException $pdoe) {
            $conn->rollBack();
            echo "Fail to retrieve last article: ".$pdoe->getMessage();
        }

    }

    public function getArticleById($aricleId){
        try {
            $conn = $this->getDBConnection();
            $conn->beginTransaction();
            $stmt = $conn->prepare("SELECT * FROM tbl_article WHERE article_id = :articleId;");
            $stmt->bindParam(":articleId", $aricleId, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $result = $stmt->fetch();
                $stmt->closeCursor();
                $conn->commit();

                //JSON Production Like API
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            }
            else{
                echo "No record is found with your search parameter";
            }
            

        } catch (PDOException $pdoe) {
            $conn->rollBack();
            echo "Fail to retrieve last article: ".$pdoe->getMessage();
        }        
    }
}




if(isset($_REQUEST['ACCESS_TOKEN']) ){
    
    if($_REQUEST['ACCESS_TOKEN']=="keyToAccess"){        
        if(isset($_REQUEST['queryName']) && $_REQUEST['queryName'] == "getLastArticle"){

            GetArticle::getInstance()->getLastArticle();
            
        }
        elseif(isset($_REQUEST['queryName']) && $_REQUEST['queryName'] == "getArticleById") {
            if(isset($_REQUEST['articleId'])){
                GetArticle::getInstance()->getArticleById($_REQUEST['articleId']);
            }
            else{
                echo "please provide article Id";
            }
            
        }
        else{
            GetArticle::getInstance()->getAllArticle();    
        }
        
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








