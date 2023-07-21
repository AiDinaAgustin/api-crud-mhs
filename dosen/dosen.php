<?php
	// Include CORS headers
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: X-Requested-With');
	header('Content-Type: application/json');

	// Include action.php file
	include_once 'fungsi.php';
	// Create object of Users class
	$user = new Fungsi();

	// create a api variable to get HTTP method dynamically
	$api = $_SERVER['REQUEST_METHOD'];

	// get id from url
	$id_dosen = intval($_GET['id_dosen'] ?? '');

	// Get all or a single user from database
	if ($api == 'GET') {
	  //cek apakah id yang dimasukan ada atau tidak, kalo ada tampilkan datanya kalo tidak ada kasih warning kalo data tidak ada
        if ($id_dosen != null) {
            $data = $user->fetch($id_dosen);
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
                'id_dosen' => $id_dosen,
                'nama_dosen' => $nama_dosen,
                'nip' => $nip,
                'alamat' => $alamat
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
        $nama_dosen = $user->test_input($_POST['nama_dosen']);
        $nip = $user->test_input($_POST['nip']);
        $alamat = $user->test_input($_POST['alamat']);
    
        if (empty($nama_dosen) || empty($nip) || empty($alamat)) {
            echo $user->message('All fields are required!', true);
        } else {
            // Attempt to insert the data into the database
            $insertResult = $user->insert($nama_dosen, $nip, $alamat);
    
            if ($insertResult === true) {
                echo $user->message('Dosen added successfully!', false);
            } elseif ($insertResult === false) {
                echo $user->message('NIP already exists!', true);
            } else {
                echo $user->message('Failed to add a Dosen!', true);
            }
        }
    }
    

	// Update an user in database
	//buatkan untuk api update
  if ($api == 'PUT') {
    parse_str(file_get_contents('php://input'), $put_input);
    $id_dosen = isset($put_input['id_dosen']) ? intval($put_input['id_dosen']) : null;
    $nama_dosen = isset($put_input['nama_dosen']) ? $user->test_input($put_input['nama_dosen']) : null;
    $nip = isset($put_input['nip']) ? $user->test_input($put_input['nip']) : null;
    $alamat = isset($put_input['alamat']) ? $user->test_input($put_input['alamat']) : null;

    if ($id_dosen != null) {
        if ($user->update($nama_dosen, $nip, $alamat, $id_dosen)) {
            echo json_encode(array('message' => 'Data dosen berhasil diperbarui.'));
        } else {
            echo json_encode(array('message' => 'Gagal memperbarui data dosen.'));
        }
    } else {
        echo json_encode(array('message' => 'ID dosen tidak ditemukan.'));
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