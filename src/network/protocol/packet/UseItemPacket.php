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

    public $unknownInt1;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::USE_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::USE_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::USE_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::USE_ITEM_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::USE_ITEM_PACKET;
		}
		return ProtocolInfo::USE_ITEM_PACKET;
	}

	public function decode(){
        //if($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9)$this->reset();
		$this->x = $this->getInt();
		$this->y = $this->getInt();
		$this->z = $this->getInt();
		$this->face = $this->getInt();
		$this->item = $this->getShort();
		$this->meta = $this->getByte(); //Mojang: fix this
		$this->eid = $this->getInt();
        if ($this->PROTOCOL > ProtocolInfo9::CURRENT_PROTOCOL_9) {
            $this->unknownInt1 = $this->getInt();
            return;
        }
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
        /*$this->reset();
        $this->putInt($this->x);
        $this->putInt($this->y);
        $this->putInt($this->z);
        $this->putInt($this->face);
        $this->putShort($this->item);
        $this->putByte($this->meta);
        $this->putInt($this->eid);
        $this->putInt($this->unknownInt1);*/
	}

}