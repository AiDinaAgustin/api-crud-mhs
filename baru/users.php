<?php
	// Include CORS headers
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: X-Requested-With');
	header('Content-Type: application/json');

	// Include action.php file
	include_once 'db.php';
	// Create object of Users class
	$user = new Database();

	// create a api variable to get HTTP method dynamically
	$api = $_SERVER['REQUEST_METHOD'];

	// get id from url
	$id = intval($_GET['id'] ?? '');

	// Get all or a single user from database
	if ($api == 'GET') {
	  //cek apakah id yang dimasukan ada atau tidak, kalo ada tampilkan datanya kalo tidak ada kasih warning kalo data tidak ada
        if ($id != null) {
            $data = $user->fetch($id);
            if (isset($data['message'])) {
            echo json_encode($data);
            } else {
            echo json_encode($data);
            }
        } else {
            $stmt = $user->fetch();
            $count = $stmt->rowCount();
    
            if ($count > 0) {
            $user_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array(
                'id' => $id,
                'nama' => $nama,
                'nim' => $nim,
                'jurusan' => $jurusan
                );
                array_push($user_arr, $user_item);
            }
            echo json_encode($user_arr);
            } else {
            echo json_encode(array('message' => 'No record found.'));
            }
        }
	}

	// Add a new user into database
	if ($api == 'POST') {
        $nama = $user->test_input($_POST['nama']);
        $nim = $user->test_input($_POST['nim']);
        $jurusan = $user->test_input($_POST['jurusan']);
    
        if (empty($nama) || empty($nim) || empty($jurusan)) {
            echo $user->message('All fields are required!', true);
        } else {
            // Attempt to insert the data into the database
            $insertResult = $user->insert($nama, $nim, $jurusan);
    
            if ($insertResult === true) {
                echo $user->message('Mahasiswa added successfully!', false);
            } elseif ($insertResult === false) {
                echo $user->message('NIM already exists!', true);
            } else {
                echo $user->message('Failed to add a Mahasiswa!', true);
            }
        }
    }
    

	// Update an user in database
	if ($api == 'PUT') {
	  parse_str(file_get_contents('php://input'), $post_input);

      $id = $user->test_input($post_input['id']);
	  $nama = $user->test_input($post_input['nama']);
	  $nim = $user->test_input($post_input['nim']);
	  $jurusan = $user->test_input($post_input['jurusan']);

	  if ($id != null) {
	    if ($user->update($nama, $nim, $jurusan, $id)) {
	      echo $user->message('Mahasiswa updated successfully!',false);
	    } else {
	      echo $user->message('Failed to update an Mahasiswa!',true);
	    }
	  } else {
	    echo $user->message('Mahasiswa not found!',true);
	  }
	}

	// Delete an user from database
	if ($api == 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = $user->test_input($_DELETE['id']);
	  if ($id != null) {
	    if ($user->delete($id)) {
	      echo $user->message('Mahasiswa deleted successfully!', false);
	    } else {
	      echo $user->message('Failed to delete an user!', true);
	    }
	  } else {
	    echo $user->message('User not found!', true);
	  }
	}

?>