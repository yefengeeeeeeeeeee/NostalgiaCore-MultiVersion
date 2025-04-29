<?php

class MoveEntityPacket extends RakNetDataPacket{

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::MOVE_ENTITY_PACKET;
        }
		return ProtocolInfo::MOVE_ENTITY_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
	}

}