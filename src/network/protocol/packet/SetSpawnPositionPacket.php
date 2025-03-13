<?php

class SetSpawnPositionPacket extends RakNetDataPacket{
	public $x;
	public $z;
	public $y;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::SET_SPAWN_POSITION_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::SET_SPAWN_POSITION_PACKET;
        }
		return ProtocolInfo::SET_SPAWN_POSITION_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
		$this->putInt($this->x);
		$this->putInt($this->z);
		$this->putByte($this->y);
	}

}