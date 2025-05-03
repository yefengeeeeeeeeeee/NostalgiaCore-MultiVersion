<?php

class SetEntityMotionPacket extends RakNetDataPacket{
	public $eid;
	public $speedX;
	public $speedY;
	public $speedZ;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::SET_ENTITY_MOTION_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::SET_ENTITY_MOTION_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::SET_ENTITY_MOTION_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::SET_ENTITY_MOTION_PACKET;
		}
		return ProtocolInfo::SET_ENTITY_MOTION_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putShort((int) ($this->speedX * 8000));
		$this->putShort((int) ($this->speedY * 8000));
		$this->putShort((int) ($this->speedZ * 8000));
	}

}