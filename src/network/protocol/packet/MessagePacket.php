<?php

class MessagePacket extends RakNetDataPacket{
	public $source;
	public $message;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return ProtocolInfo3::MESSAGE_PACKET;
		}
		return ProtocolInfo::MESSAGE_PACKET;
	}

	public function decode(){
		if($this->PROTOCOL > ProtocolInfo9::CURRENT_PROTOCOL_9){ //0.6.1 and below From NostalgiaCore-BackPort Author:Gameherobrine
			$this->source = $this->getString();
		}else {
			$this->source = "";
		}
		$this->message = $this->getString();
	}

	public function encode(){
		$this->reset();
		if($this->PROTOCOL > ProtocolInfo9::CURRENT_PROTOCOL_9){ //0.6.1 and below From NostalgiaCore-BackPort Author:Gameherobrine
			$this->putString($this->source);
		}
		$this->putString($this->message);
	}

}