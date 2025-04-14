<?php

class HurtArmorPacket extends RakNetDataPacket{
	public $health;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::HURT_ARMOR_PACKET;
        }
		return ProtocolInfo::HURT_ARMOR_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
		$this->putByte($this->health);
	}

}