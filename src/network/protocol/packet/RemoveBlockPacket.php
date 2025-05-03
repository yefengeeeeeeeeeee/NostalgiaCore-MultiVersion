<?php

class RemoveBlockPacket extends RakNetDataPacket{
	public $eid;
	public $x;
	public $y;
	public $z;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::REMOVE_BLOCK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::REMOVE_BLOCK_PACKET;
		}
		return ProtocolInfo::REMOVE_BLOCK_PACKET;
	}

	public function decode(){
        //if($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9)$this->reset();
		$this->eid = $this->getInt();
		$this->x = $this->getInt();
		$this->z = $this->getInt();
		$this->y = $this->getByte();
	}

	public function encode(){
        /*$this->reset();
        $this->putInt($this->eid);
        $this->putInt($this->x);
        $this->putInt($this->z);
        $this->putInt($this->y);*/
	}

}