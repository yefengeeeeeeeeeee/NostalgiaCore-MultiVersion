<?php

class ContainerSetDataPacket extends RakNetDataPacket{
	public $windowid;
	public $property;
	public $value;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
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