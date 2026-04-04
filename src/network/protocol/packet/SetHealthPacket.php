<?php

class SetHealthPacket extends RakNetDataPacket{
	public $health;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::SET_HEALTH_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::SET_HEALTH_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::SET_HEALTH_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::SET_HEALTH_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::SET_HEALTH_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::SET_HEALTH_PACKET;
		}
		return ProtocolInfo::SET_HEALTH_PACKET;
	}

	public function decode(){
		$this->health = $this->getByte();
	}

	public function encode(){
		$this->reset();
		$this->putByte($this->health);
	}

}