<?php

class UnknownPacket extends RakNetDataPacket{
	public $packetID = -1;
	
	public function __construct($pid = -1){
		$this->packetID = $pid;
	}
	
	public function pid(){
		return $this->packetID;
	}
	
	public function decode(){

	}
	
	public function encode(){

	}

}