<?php

class DropItemPacket extends RakNetDataPacket{
	public $eid;
	public $randomly;
	public $item;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
			return  ProtocolInfo4::DROP_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo5::DROP_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::DROP_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::DROP_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_11){
			return  ProtocolInfo9::DROP_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::DROP_ITEM_PACKET;
		}
		return ProtocolInfo::DROP_ITEM_PACKET;
	}

	public function decode(){
		$this->eid = $this->getInt();
		$this->randomly = $this->getByte();
		$this->item = $this->getSlot();
	}

	public function encode(){
	}
	
	public function eidsToGlobal(Player $p){
		if($this->localEids){
			$this->localEids = false;
			$this->eid = $p->local2GlobalEID[$this->eid] ?? false;
			if($this->eid === false) return false;
		}
		return true;
	}
}
