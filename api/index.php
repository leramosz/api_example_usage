<?
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
	header("Access-Control-Allow-Headers: X-Requested-With");

	//discovering HTTP method
 	$method = $_SERVER['REQUEST_METHOD'];
	
 	//connecting to the mysql database
	$link = new mysqli('localhost', 'leo', 'CpVAnIZLJPHiUPk4', 'test');
	
	//inicializing output json data
	$output = array();	

	//verifying HTTP method
	switch ($method) {

		case 'GET':

			//List all elements
			$sql = "SELECT * FROM ".$_REQUEST['table'];

			//List element by ID			
			if (isset($_REQUEST['id'])) { 
				$sql .= " WHERE id=".$_REQUEST['id'];
			}
			
			//Getting the result and craeting output data array
			$result = $link->query($sql);
			while($row = $result->fetch_assoc()) {
				$output[] = $row;
			}

		break;

		case 'POST':

			//Getting the input json data
			$input = json_decode(file_get_contents('php://input'),true);

			//Creating the fields and values list
			$columns = implode(",",array_keys($input));
			$escaped_values = array_map(array($link, 'real_escape_string'), array_values($input));
			array_walk($escaped_values, function(&$item) { $item = "'".$item."'"; });
			$values  = implode(",", $escaped_values);

			//Inserting the data
			$sql = "INSERT INTO ".$_REQUEST['table']."(".$columns.") VALUES (".$values.")";
			$link->query($sql);

			//Preparing the output data
			$input['id'] = $link->insert_id;
			$output = $input;

		break;
		
		case 'PUT':
			
			//Getting the input json data
			$input = json_decode(file_get_contents('php://input'),true);

			//Preparing the fields and values to update
			$update_statement = array();
			foreach ($input as $key => $value) {
				$value = "'".$link->real_escape_string($value)."'";
				$update_statement[] = $key." = ".$value;
			}
		
			//Updating the data by ID
			$sql = "UPDATE ".$_REQUEST['table']." SET ".implode(", ", $update_statement)." WHERE id=".$_REQUEST['id'];
			$link->query($sql);

			//Preparing the output data
			$input['id'] = $_REQUEST['id'];
			$output = $input;


		break;

		case 'DELETE':

			//Deleting the data by ID
			$sql = "DELETE FROM ".$_REQUEST['table']." WHERE id=".$_REQUEST['id'];
			$link->query($sql);

		break;
	}

	//Returning output json data
	echo json_encode($output);

?>