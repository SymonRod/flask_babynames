<?php

	function check_account($name, $conn) {
		$exist = false;

		$sql = "SELECT * FROM user WHERE username='" . $name . "'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$exist = true;
		} else {
			$exist = false;
		}

		return $exist;

	}

	$host = "localhost";
	$db_name = "id11905215_test";
	$username = "id11905215_test";
	$password = "Manzetti2020";

	$conn = new mysqli($host, $username, $password, $db_name);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$json = array();
		
		if (isset($_POST['user'])) {
			$user = $_POST['user'];
			$utype = explode(".", $user)[0];

			$sql = "SELECT documents.nome, documents.url FROM documents WHERE categoria='" . $utype . "'";
			$result = $conn->query($sql);

			if (check_account($user, $conn)) {
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						//Array associativo
						array_push($json, array("nome"=>$row["nome"], "url"=>$row["url"]));
					}
					echo json_encode($json);
				} else {
					echo "0 results";
				}
			} else {
				http_response_code(404);
				echo json_encode(array("error"=>"The provided account was not found!"));
			}
		} else { //Condizione da migliorare
			$username = $_REQUEST["username"];
			$classe = $_REQUEST["classe"];
			if (isset($_POST["studente"]))
				$studente = $_REQUEST["studente"];
			if (isset($insegnante))
				$insegnante = $_REQUEST["insegnante"];

			echo $_REQUEST["submit"];

			if (isset($studente) and !isset($insegnante))
				$sql = "INSERT INTO user (class,tipo,username) VALUES ('$classe', '$studente', '$username')";
			elseif (!isset($studente) and isset($insegnante))
				$sql = "INSERT INTO user (class,tipo,username) VALUES ('$classe', '$insegnante', '$username')";

			if ($conn->query($sql) === TRUE) {
			    echo "New record created successfully";
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}

		}
	}


	$conn->close();
?>