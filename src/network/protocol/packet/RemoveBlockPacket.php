<?php

class RemoveBlockPacket extends RakNetDataPacket{
	public $eid;
	public $x;
	public $y;
	public $z;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::REMOVE_BLOCK_PACKET;
        }
		return ProtocolInfo::REMOVE_BLOCK_PACKET;
	}
	
	public function decode(){
		$this->eid = $this->getInt();
		$this->x = $this->getInt();
		$this->z = $this->getInt();
		$this->y = $this->getByte();
	}
	
	public function encode(){

	}

}