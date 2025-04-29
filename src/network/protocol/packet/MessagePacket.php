<?php

class MessagePacket extends RakNetDataPacket{
	public $source;
	public $message;

	public function pid(){
		return ProtocolInfo::MESSAGE_PACKET;
	}

	public function decode(){
		if($this->PROTOCOL > ProtocolInfo9::CURRENT_PROTOCOL_9){ //0.6.1 and below From NostalgiaCore-BackPort Author:Gameherobrine
			$this->source = $this->getString();
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