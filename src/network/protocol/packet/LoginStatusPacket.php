<?php

class LoginStatusPacket extends RakNetDataPacket{
	public $status;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return ProtocolInfo3::LOGIN_STATUS_PACKET;
		}
		return ProtocolInfo::LOGIN_STATUS_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->status);
	}

}