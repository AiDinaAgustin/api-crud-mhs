<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include_once 'api/Database.php';
include_once 'api/Mahasiswa.php';

$database = new Database();
$db = $database->connect();

$mahasiswa = new Mahasiswa($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Cek apakah ada parameter 'id' di URL
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $data = $mahasiswa->read($id);
    
            if (isset($data['message'])) {
                // Jika data tidak ditemukan, tampilkan pesan
                echo json_encode($data);
            } else {
                // Jika data ditemukan, tampilkan data mahasiswa berdasarkan ID
                echo json_encode($data);
            }
        } else {
            // Jika tidak ada parameter 'id' di URL, tampilkan semua data mahasiswa
            $stmt = $mahasiswa->read();
            $count = $stmt->rowCount();
    
            if ($count > 0) {
                $mahasiswa_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $mahasiswa_item = array(
                        'id' => $id,
                        'nama' => $nama,
                        'nim' => $nim,
                        'jurusan' => $jurusan
                    );
                    array_push($mahasiswa_arr, $mahasiswa_item);
                }
                echo json_encode($mahasiswa_arr);
            } else {
                echo json_encode(array('message' => 'Tidak ada data mahasiswa.'));
            }
        }
        break; 
        
        case 'POST':
            // Get the POST data and decode the JSON
            $data = json_decode(file_get_contents("php://input"));
            // Make sure all required fields are provided
            if (!empty($data->nama) && !empty($data->nim) && !empty($data->jurusan)) {
                $mahasiswa->nama = $data->nama;
                $mahasiswa->nim = $data->nim;
                $mahasiswa->jurusan = $data->jurusan;
    
                if ($mahasiswa->create()) {
                    echo json_encode(array('message' => 'Mahasiswa telah ditambahkan.'));
                } else {
                    echo json_encode(array('message' => 'Gagal menambahkan mahasiswa.'));
                }
            } else {
                echo json_encode(array('message' => 'Semua field harus diisi.'));
            }
            break;
    
        case 'PUT':
            // Get the PUT data and decode the JSON
            $data = json_decode(file_get_contents("php://input"));
            // Make sure all required fields are provided
            if (!empty($data->id) && !empty($data->nama) && !empty($data->nim) && !empty($data->jurusan)) {
                $mahasiswa->id = $data->id;
                $mahasiswa->nama = $data->nama;
                $mahasiswa->nim = $data->nim;
                $mahasiswa->jurusan = $data->jurusan;
    
                if ($mahasiswa->edit()) {
                    echo json_encode(array('message' => 'Mahasiswa telah diubah.'));
                } else {
                    echo json_encode(array('message' => 'Gagal mengubah mahasiswa.'));
                }
            } else {
                echo json_encode(array('message' => 'Semua field harus diisi.'));
            }
            break;
    
        case 'DELETE':
            // Get the DELETE data and decode the JSON
            $data = json_decode(file_get_contents("php://input"));
            // Make sure the ID is provided
            if (!empty($data->id)) {
                $mahasiswa->id = $data->id;
    
                if ($mahasiswa->delete()) {
                    echo json_encode(array('message' => 'Mahasiswa telah dihapus.'));
                } else {
                    echo json_encode(array('message' => 'Gagal menghapus mahasiswa.'));
                }
            } else {
                echo json_encode(array('message' => 'ID mahasiswa harus disediakan.'));
            }
            break;

    default:
        echo json_encode(array('message' => 'Metode tidak didukung.'));
        break;
}
?>
