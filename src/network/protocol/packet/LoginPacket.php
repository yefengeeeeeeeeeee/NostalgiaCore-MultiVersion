<?php

class LoginPacket extends RakNetDataPacket{
	public $username;
	public $protocol1;
	public $protocol2;
	public $clientId;
	public $loginData;

	public function pid(){
		return ProtocolInfo::LOGIN_PACKET;
	}

	public function decode(){
        //if($this->PROTOCOL <= ProtocolInfo9::CURRENT_PROTOCOL_9)$this->reset();
		$this->username = $this->getString();
		$this->protocol1 = $this->getInt();
		$this->protocol2 = $this->getInt();
		$this->clientId = $this->getInt();//null
		$this->loginData = $this->getString();
	    $this->PROTOCOL = $this->protocol1;
	}

	public function encode(){

	}

}