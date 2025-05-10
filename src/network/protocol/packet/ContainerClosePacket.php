<?php

class ContainerClosePacket extends RakNetDataPacket{
	public $windowid;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::CONTAINER_CLOSE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::CONTAINER_CLOSE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::CONTAINER_CLOSE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::CONTAINER_CLOSE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
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