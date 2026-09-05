<?php

class ReadyPacket extends RakNetDataPacket{
	public $status;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return ProtocolInfo3::READY_PACKET;
		}
		return ProtocolInfo::READY_PACKET;
	}

	public function decode(){
        //if($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9)$this->reset();
		$this->status = $this->getByte();
	}

	public function encode(){
        /*$this->reset();
        $this->putByte($this->status);*/
	}

}