<?php

class ReadyPacket extends RakNetDataPacket{
	public $status;

	public function pid(){
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