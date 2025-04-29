<?php

class AddItemEntityPacket extends RakNetDataPacket{
	public $eid;
	public $item;
	public $x;
	public $y;
	public $z;
	public $yaw;
	public $pitch;
	public $roll;

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::ADD_ITEM_ENTITY_PACKET;
        }
		return ProtocolInfo::ADD_ITEM_ENTITY_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putSlot($this->PROTOCOL, $this->item);
		$this->putFloat($this->x);
		$this->putFloat($this->y);
		$this->putFloat($this->z);
		$this->putByte($this->yaw);
		$this->putByte($this->pitch);
		$this->putByte($this->roll);
	}

}