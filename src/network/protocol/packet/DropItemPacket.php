<?php

class DropItemPacket extends RakNetDataPacket{
	public $eid;
	public $unknown;
	public $item;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::DROP_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::DROP_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::DROP_ITEM_PACKET;
        }
		return ProtocolInfo::DROP_ITEM_PACKET;
	}
	
	public function decode(){
		$this->eid = $this->getInt();
		$this->unknown = $this->getByte();
		$this->item = $this->getSlot();
	}
	
	public function encode(){

	}

}