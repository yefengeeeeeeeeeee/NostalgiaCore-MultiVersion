<?php

class LevelEventPacket extends RakNetDataPacket{

	const EVENT_OPEN_DOOR_SOUND = 1003;
	const EVENT_ALL_PLAYERS_SLEEPING = 9800;

	public $evid;
	public $x;
	public $y;
	public $z;
	public $data;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::LEVEL_EVENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::LEVEL_EVENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::LEVEL_EVENT_PACKET;
		}
		return ProtocolInfo::LEVEL_EVENT_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putShort($this->evid);
		$this->putShort($this->x);
		$this->putShort($this->y);
		$this->putShort($this->z);
		$this->putInt($this->data);
	}

}