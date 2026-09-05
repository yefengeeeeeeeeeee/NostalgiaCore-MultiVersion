<?php

class TakeItemEntityPacket extends RakNetDataPacket{
	public $target;
	public $eid;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::TAKE_ITEM_ENTITY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo5::TAKE_ITEM_ENTITY_PACKET;
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

	//TODO convert
	public function eidsToLocal(Player $p){
		if(!$this->localEids){
			$this->localEids = true;
			$this->eid = $p->global2localEID[$this->eid] ?? false;
			$this->target = $p->global2localEID[$this->target] ?? false;
			if($this->eid === false || $this->target === false) return false;
		}
		return true;
	}
}