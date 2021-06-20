<?php
    if(isset($_GET["nome"]) && isset($_GET["lingua"]))    {
        $host = "localhost";
        $db_name = "id11905215_babynames";
        $username = "id11905215_babynames_";
        $password = "Ciaone1234!!";
        
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
                http_response_code(400);
                return;
            }
        } 
        if(!$json)  {
            header('Content-type: text/xml');
            $response="<?xml version=\"1.0\" encoding=\"UTF-8\"?><Nomi>";
        }


        $conn = new mysqli($host, $username, $password, $db_name);

        if ($conn->connect_error) {
            //echo('Conn fallita');
            die("Connection failed: " . $conn->connect_error);
            return;
        }
        $query ="";

        foreach($lingue as $lingua) {
            switch($lingua) {
                case ("IT"):
                    //print("IT");
                    $query.="SELECT nome,lingua FROM nomi WHERE nome LIKE '$nome%' ORDER BY nome ASC;";
                    break;
                case ("EN"):
                    //print("EN");
                    $query.="SELECT nome,lingua FROM nomi_en WHERE nome LIKE '$nome%' ORDER BY nome ASC;";
                    break;
            }
        }
        $risultati = array();
        //print($query);
        if ($conn->multi_query($query)) {
            do {
                /* store first result set */
                if ($result = $conn->store_result()) {
                    while ($row = $result->fetch_row()) {
                        $valore = $row[0];
                        $lingua = $row[1];
                        
                        if($json)   {
                            $nome = array("valore"=>$valore, "lingua"=>$lingua);
                            array_push($risultati,$nome);
                        } else{
                            $response.="<nome><valore>$valore</valore><lingua>$lingua</lingua></nome>";
                        }
                        //printf("%s\n", $row[0]);
                    }
                    $result->free();
                }
                /* print divider */
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
            echo($response);
        }

    }
    http_response_code(400);