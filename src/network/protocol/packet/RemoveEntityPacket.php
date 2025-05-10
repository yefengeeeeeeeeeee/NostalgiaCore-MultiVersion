<?php

class RemoveEntityPacket extends RakNetDataPacket{
	public $eid;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::REMOVE_ENTITY_PACKET;
		}
		return ProtocolInfo::REMOVE_ENTITY_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
	}

}