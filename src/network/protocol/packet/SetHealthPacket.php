<?php

class SetHealthPacket extends RakNetDataPacket{
	public $health;

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::SET_HEALTH_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::SET_HEALTH_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::SET_HEALTH_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::SET_HEALTH_PACKET;
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