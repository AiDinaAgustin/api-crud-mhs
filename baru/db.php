<?php
	// Include config.php file
	include_once 'config.php';

	// Create a class Users
	class Database extends Config {
	  // Fetch all or a single user from database
	  public function fetch($id = null) {
	    if ($id !== null) {
            $query = 'SELECT * FROM mahasiswa WHERE id = :id';
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
            $query = 'SELECT * FROM mahasiswa';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            // Return the PDOStatement object directly, not fetchAll result
            return $stmt;
        }
	  }

	  // Insert an user in the database
	  public function insert($nama, $nim, $jurusan) {
        // Check if the given nim already exists in the database
        $checkQuery = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':nim', $nim, PDO::PARAM_STR);
        $checkStmt->execute();
        $nimExists = $checkStmt->fetchColumn();
    
        if ($nimExists > 0) {
            // If the nim already exists, return false to indicate failure
            return false;
        }
    
        // Proceed with the insert operation since the nim doesn't exist
        $sql = 'INSERT INTO mahasiswa (nama, nim, jurusan) VALUES (:nama, :nim, :jurusan)';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['nama' => $nama, 'nim' => $nim, 'jurusan' => $jurusan]);
    
        // Return true to indicate success
        return true;
    }
    

	  // Update an user in the database
	  public function update($nama, $nim, $jurusan, $id) {
	    $sql = 'UPDATE mahasiswa SET nama = :nama, nim = :nim, jurusan = :jurusan WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['nama' => $nama, 'nim' => $nim, 'jurusan' => $jurusan, 'id' => $id]);
	    return true;
	  }

	  // Delete an user from database
	  public function delete($id) {
	    $sql = 'DELETE FROM mahasiswa WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['id' => $id]);
	    return true;
	  }
	}

?>