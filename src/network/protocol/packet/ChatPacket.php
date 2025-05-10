<?php

class ChatPacket extends RakNetDataPacket{
	public $message;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::CHAT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::CHAT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::CHAT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::CHAT_PACKET;
		}
		return ProtocolInfo::CHAT_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putString($this->message);
	}

}