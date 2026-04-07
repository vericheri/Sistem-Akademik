<?php
abstract class user {
    protected $id;
    protected $name;
    protected $email;

    abstract public function getrole();
    
    public function __construct($id, $nama, $email) {
        $this->id = $id;
        $this->nama = $nama;
        $this->email = $email;
    }

    public function getnama() {
        return $this->nama;
    }
}
?>