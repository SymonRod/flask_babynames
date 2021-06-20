<?php

    if(isset($_GET["nome"]) && isset($_GET["lingua"]))    {

        $host = "localhost";
        $db_name = "my_rodopo";
        $username = "rodopo";
        $password = "";
        
        $json=false;
        $xml=true;
        $nome = $_GET['nome'];
        $lingue = explode(",",$_GET['lingua']);

        if(isset($_GET["type"]))    {
            if($_GET["type"]=='json') {
                header('Content-Type: application/json');
                $json=true;
                $xml = false;
            } else if($_GET["type"] == 'xml')   {
                header('Content-type: text/xml');
                $response="<?xml version=\"1.0\" encoding=\"UTF-8\"?><Nomi>";
            } else {
                echo('Syntax Error');
                return;
            }
        } 

        if(!$json)  {
            header('Content-type: text/xml');
            $response="<?xml version=\"1.0\" encoding=\"UTF-8\"?><Nomi>";
        }

        $conn = new mysqli($host, $username, $password, $db_name);

        if ($conn->connect_error) {
            echo('Conn fallita');
            die("Connection failed: " . $conn->connect_error);
            return;
        }

        $query ="";
        $idNomi = array();

        foreach($lingue as $lingua) {
            switch($lingua) {
                case ("IT"):
                    $query.="SELECT Nomi.nome, Lingue.lingua, Nomi.id FROM Nomi INNER JOIN Lingue ON Lingue.lingua = 'IT' WHERE Nomi.nome LIKE '$nome%' AND Nomi.id_lingua = (SELECT Lingue.id FROM Lingue WHERE  Lingue.lingua = 'IT');";
                    break;
                case ("EN"):
                    $query.="SELECT Nomi.nome, Lingue.lingua, Nomi.id FROM Nomi INNER JOIN Lingue ON Lingue.lingua = 'EN' WHERE Nomi.nome LIKE '$nome%' AND Nomi.id_lingua = (SELECT Lingue.id FROM Lingue WHERE  Lingue.lingua = 'EN');";
                    break;
            }
        }
        $risultati = array();
        if ($conn->multi_query($query)) {
            do {
                if ($result = $conn->store_result()) {

                    while ($row = $result->fetch_row()) {
                        $valore = $row[0];
                        $lingua = $row[1];
                        $id = $row[2];
                        if($json)   {
                            $nome = (array("nome"=>array("id"=>$id,"valore"=>$valore, "lingua"=>$lingua)));
                            array_push($risultati,$nome);
                        } else{
                            $response.="<nome><valore>$valore</valore><lingua>$lingua</lingua><id>$id</id></nome>";
                        }
                    }
                    $result->free();
                }
                if ($conn->more_results()) {
                }
            } while ($conn->next_result());
        }
        mysqli_close($conn);
        http_response_code(200);

        if($json){
            echo json_encode($risultati);
        } else {
            $response.="</Nomi>";
            utf8_encode($response);
            echo($response);
        }

    } else if($_GET['idNome'])   {
        //echo('Funziono');
        $host = "localhost";
        $db_name = "my_rodopo";
        $username = "rodopo";
        $password = "";
        
        $json=false;

        if(isset($_GET["type"]))    {
            if($_GET["type"] == 'json') {
                header('Content-Type: application/json');
                $json=true;
                $xml = false;
            } else if($_GET["type"] == 'xml')   {
                header('Content-type: text/xml');
                $response="<?xml version=\"1.0\" encoding=\"UTF-8\"?><MiPiace>";
            }
        }

        if(!$json)  {
            header('Content-type: text/xml');
            $response="<?xml version=\"1.0\" encoding=\"UTF-8\"?><MiPiace>";
        }

        
        $conn = new mysqli($host, $username, $password, $db_name);
        $id = $_GET['idNome'];
        $query = "SELECT valore FROM MiPiace WHERE id_nome='$id';";
        if ($conn->connect_error) {
            echo('Conn fallita');
            die("Connection failed: " . $conn->connect_error);
            return;
        }

        $result = mysqli_query($conn,$query);
        $risultati = array();

        if(mysqli_num_rows($result) > 0)    {
            while($row = mysqli_fetch_assoc($result))   {
                foreach($row as $name => $field)    {
                    if(!$json)   {
                        $response.="<$name>"
                        .$field
                        ."</$name>";
                    } else {
                        array_push($risultati,(array("$name",$field)));
                    }
                }
            }
        }

        mysqli_close($conn);

        if($json){
            echo json_encode($risultati);
        } else {
            $response.="</MiPiace>";
            echo($response);
        }
    }