<?php

class TakeItemEntityPacket extends RakNetDataPacket{
	public $target;
	public $eid;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::TAKE_ITEM_ENTITY_PACKET;
		}
		return ProtocolInfo::TAKE_ITEM_ENTITY_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->target);
		$this->putInt($this->eid);
	}

}