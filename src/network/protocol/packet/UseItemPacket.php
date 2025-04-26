<?php

class UseItemPacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $face;
	public $item;
	public $meta;
	public $eid;
	public $fx;
	public $fy;
	public $fz;
	public $posX;
	public $posY;
	public $posZ;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::USE_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::USE_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::USE_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::USE_ITEM_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::USE_ITEM_PACKET;
        }
		return ProtocolInfo::USE_ITEM_PACKET;
	}
	
	public function decode(){
		$this->x = $this->getInt();
		$this->y = $this->getInt();
		$this->z = $this->getInt();
		$this->face = $this->getInt();
		$this->item = $this->getShort();
		$this->meta = $this->getByte(); //Mojang: fix this
		$this->eid = $this->getInt();
		$this->fx = $this->getFloat();
		$this->fy = $this->getFloat();
		$this->fz = $this->getFloat();
        if ($this->PROTOCOL > ProtocolInfo9::CURRENT_PROTOCOL_9) {
            $this->posX = $this->getFloat();
            $this->posY = $this->getFloat();
            $this->posZ = $this->getFloat();
        }
	}
	
	public function encode(){

	}

}