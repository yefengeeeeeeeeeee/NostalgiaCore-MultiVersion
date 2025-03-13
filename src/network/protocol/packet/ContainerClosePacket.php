<?php

class ContainerClosePacket extends RakNetDataPacket{
	public $windowid;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::CONTAINER_CLOSE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::CONTAINER_CLOSE_PACKET;
        }
		return ProtocolInfo::CONTAINER_CLOSE_PACKET;
	}
	
	public function decode(){
		$this->windowid = $this->getByte();
	}
	
	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
	}

}