<?php

class DropItemPacket extends RakNetDataPacket{
	public $eid;
	public $unknown;
	public $item;

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::DROP_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::DROP_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::DROP_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
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