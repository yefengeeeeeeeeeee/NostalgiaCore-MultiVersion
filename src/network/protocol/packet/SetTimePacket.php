<?php

class SetTimePacket extends RakNetDataPacket{
	public $time;
	public $started = true;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return ProtocolInfo3::SET_TIME_PACKET;
		}
		return ProtocolInfo::SET_TIME_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->time);// long in 0.6.1
        if($this->PROTOCOL >= ProtocolInfo8::CURRENT_PROTOCOL_8){
            $this->putByte($this->started ? 0x80:0x00);
        }
	}

}