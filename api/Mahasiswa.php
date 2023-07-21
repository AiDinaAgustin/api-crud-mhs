<?php
class Mahasiswa {
    public $conn;
    public $table = 'mahasiswa';
    public $id;
    public $nama;
    public $nim;
    public $jurusan;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($id = null) {
        if ($id !== null) {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($data === false) {
                return array("message" => "Data tidak ditemukan.");
            } else {
                return $data;
            }
        } else {
            $query = 'SELECT * FROM ' . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            // Return the PDOStatement object directly, not fetchAll result
            return $stmt;
        }
    }    

   public function create(){
    // Pengecekan apakah NIM sudah ada dalam tabel
    $check_sql = 'SELECT id FROM' .$this->table. 'WHERE nim = :nim';
    $check_result = $this->conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        // Jika NIM sudah ada, tampilkan pesan warning
        echo json_encode(array("message" => "NIM sudah ada. Mahasiswa tidak ditambahkan."));
    } else {
        // Jika NIM belum ada, lakukan penambahan data
        $sql = 'INSERT INTO' .$this->table. '(nama, nim, jurusan) VALUES (:nama, :nim, :jurusan)';

        if ($this->conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Mahasiswa berhasil ditambahkan."));
        } else {
            echo json_encode(array("message" => "Error: " . $sql . "<br>" . $this->conn->error));
        }
    }
   }
    
    public function edit() {
        $query = 'UPDATE ' . $this->table . ' SET nama = :nama, nim = :nim, jurusan = :jurusan WHERE id = :id';
        $stmt = $this->conn->prepare($query);
    
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->nim = htmlspecialchars(strip_tags($this->nim));
        $this->jurusan = htmlspecialchars(strip_tags($this->jurusan));
    
        // Bind data
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':nim', $this->nim);
        $stmt->bindParam(':jurusan', $this->jurusan);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
    
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Bind data
        $stmt->bindParam(':id', $this->id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
