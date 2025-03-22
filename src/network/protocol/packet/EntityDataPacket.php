<?php

class EntityDataPacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $namedtag;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::ENTITY_DATA_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::ENTITY_DATA_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::ENTITY_DATA_PACKET;
        }
		return ProtocolInfo::ENTITY_DATA_PACKET;
	}
	
	public function decode(){
		$this->x = $this->getShort();
		$this->y = $this->getByte();
		$this->z = $this->getShort();
		$this->namedtag = $this->get(true);
	}
	
	public function encode(){
		$this->reset();
		$this->putShort($this->x);
		$this->putByte($this->y);
		$this->putShort($this->z);
		$this->put($this->namedtag);
	}

}