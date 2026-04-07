<?php
require_once 'laporan.php';
require_once 'user.php';

class dosen extends user implements laporan {
    private $nidn;
    private $fakultas;

    public function __construct($id, $nama, $email, $nidn, $fakultas) {
        parent::__construct($id, $nama, $email);
        $this->nidn =$nidn;
        $this->fakultas = $fakultas;
    }

    public function getrole() {
        return "dosen";
    }

    public function cetaklaporan() {
        return "<p>Laporan Dosen: {$this->nama} (NIDN: {$this->nidn}) - Fakultas {$this->fakultas}</p>";
    }    
}
?>

