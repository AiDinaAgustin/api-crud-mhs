<?php
	// Include config.php file
	include_once 'config.php';

	// Create a class Users
	class Fungsi extends Config {
	  // Fetch all or a single user from database
	  public function fetch($id_dosen = null) {
	    if ($id_dosen !== null) {
            $query = 'SELECT * FROM dosen WHERE id_dosen = :id_dosen';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_dosen', $id_dosen, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($data === false) {
                return array("message" => "Data tidak ditemukan.");
            } else {
                return $data;
            }
        } else {
            $query = 'SELECT * FROM dosen';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            // Return the PDOStatement object directly, not fetchAll result
            return $stmt;
        }
	  }

	  // Insert an user in the database
	  public function insert($nama_dosen, $nip, $alamat) {
        // Check if the given nim already exists in the database
        $checkQuery = 'SELECT COUNT(*) FROM dosen WHERE nip = :nip';
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':nip', $nip, PDO::PARAM_STR);
        $checkStmt->execute();
        $nimExists = $checkStmt->fetchColumn();
    
        if ($nimExists > 0) {
            // If the nim already exists, return false to indicate failure
            return false;
        }
    
        // Proceed with the insert operation since the nim doesn't exist
        $sql = 'INSERT INTO dosen (nama_dosen, nip, alamat) VALUES (:nama_dosen, :nip, :alamat)';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['nama_dosen' => $nama_dosen, 'nip' => $nip, 'alamat' => $alamat]);
    
        // Return true to indicate success
        return true;
    }
    

	  // Update an user in the database
	  public function update($nama_dosen, $nip, $alamat, $id_dosen) {
	    $sql = 'UPDATE dosen SET nama_dosen = :nama_dosen, nip = :nip, alamat = :alamat WHERE id_dosen = :id_dosen';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['nama_dosen' => $nama_dosen, 'nip' => $nip, 'alamat' => $alamat, 'id_dosen' => $id_dosen]);
	    return true;
	  }

	  // Delete an user from database
	  public function delete($id) {
	    $sql = 'DELETE FROM dosen WHERE id_dosen = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['id_dosen' => $id]);
	    return true;
	  }
	}

?>