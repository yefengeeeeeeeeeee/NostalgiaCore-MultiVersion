<?php

class SignUpdatePacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $content;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::SIGN_UPDATE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::SIGN_UPDATE_PACKET) {
            return ProtocolInfo8::SIGN_UPDATE_PACKET;
        }
		return ProtocolInfo9::SIGN_UPDATE_PACKET;
	}

	public function decode(){
        //$this->reset();
        $this->x = $this->getShort();
        $this->y = $this->getByte();
        $this->z = $this->getShort();
        $this->content = $this->getString();
	}

	public function encode(){
		//$this->reset();
		$this->putShort($this->x);
		$this->putByte($this->y);
		$this->putShort($this->z);
		$this->putString($this->content);
	}

}