<?php

class MoveEntityPacket extends RakNetDataPacket{

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::MOVE_ENTITY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo5::MOVE_ENTITY_PACKET;
		}
		return ProtocolInfo::MOVE_ENTITY_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset(); //null
	}

}