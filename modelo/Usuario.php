<?php

class Usuario {
    private $nickname;
    private $passwd;
    private $nGanadas;
    private $nPartidas;


    function __construct($nickname, $passwd, $nGanadas, $nPartidas) {
        $this->nickname = $nickname;
        $this->passwd = $passwd;
        $this->nGanadas = $nGanadas;
        $this->nPartidas = $nPartidas;
    }
    

    public function getNickname() {
        return $this->nickname;
    }

    public function getPasswd() {
        return $this->passwd;
    }

    public function getNPartidas() {
        return $this->nPartidas;
    }

    public function getNGanadas() {
        return $this->nGanadas;
    }
 

    public function setNGanadas($nGanadas) {
        $this->nGanadas = $nGanadas;
        return $this;
    }
 
    public function setNPartidas($nPartidas) {
        $this->nPartidas = $nPartidas;
        return $this;
    }
 
    public function setPasswd($passwd) {
        $this->passwd = $passwd;
        return $this;
    }
}