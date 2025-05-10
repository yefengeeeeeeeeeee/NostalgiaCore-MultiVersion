<?php
/*
Lets hope it will work
*/
class SetEntityLinkPacket extends RakNetDataPacket{

	const TYPE_REMOVE = 0;
	const TYPE_RIDE = 1;
	const TYPE_PASSENGER = 2;

	public $rider;
	public $riding;
	public $type;
	public function decode() {

	}
	public function encode() {
		$this->reset();
		$this->putInt($this->rider);
		$this->putInt($this->riding);
		$this->putInt($this->type);
	}

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::SET_ENTITY_LINK_PACKET;
		}
		return ProtocolInfo::SET_ENTITY_LINK_PACKET;
	}

}