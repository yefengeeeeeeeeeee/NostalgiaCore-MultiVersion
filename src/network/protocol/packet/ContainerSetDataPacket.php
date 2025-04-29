<?php

class ContainerSetDataPacket extends RakNetDataPacket{
	public $windowid;
	public $property;
	public $value;

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::CONTAINER_SET_DATA_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::CONTAINER_SET_DATA_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::CONTAINER_SET_DATA_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo7::CONTAINER_SET_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::CONTAINER_SET_DATA_PACKET;
        }else if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::CONTAINER_SET_DATA_PACKET;
        }
		return ProtocolInfo::CONTAINER_SET_DATA_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
		$this->putShort($this->property);
		$this->putShort($this->value);
	}

}