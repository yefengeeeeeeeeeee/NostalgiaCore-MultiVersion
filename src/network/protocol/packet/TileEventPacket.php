<?php

class TileEventPacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $case1;
	public $case2;

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::TILE_EVENT_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::TILE_EVENT_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::TILE_EVENT_PACKET;
        }
		return ProtocolInfo::TILE_EVENT_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->x);
		$this->putInt($this->y);
		$this->putInt($this->z);
		$this->putInt($this->case1);
		$this->putInt($this->case2);
	}

}