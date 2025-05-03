<?php

class EntityEventPacket extends RakNetDataPacket{
	public $eid;
	public $event;

	const ENTITY_DAMAGE = 2;
	const ENTITY_DEAD = 3;
	const ENTITY_ANIM_10 = 10;

	public function __construct($eid = null, $event = null){
		$this->eid = $eid;
		$this->event = $event;
	}

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::ENTITY_EVENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::ENTITY_EVENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::ENTITY_EVENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::ENTITY_EVENT_PACKET;
		}
		return ProtocolInfo::ENTITY_EVENT_PACKET;
	}

	public function decode(){
		$this->eid = $this->getInt();
		$this->event = $this->getByte();
	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putByte($this->event);
	}

}