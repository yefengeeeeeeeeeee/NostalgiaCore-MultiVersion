<?php

class UpdateBlockPacket extends RakNetDataPacket{
	public $x;
	public $z;
	public $y;
	public $block;
	public $meta;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
			return  ProtocolInfo4::UPDATE_BLOCK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::UPDATE_BLOCK_PACKET;
		}
		return ProtocolInfo::UPDATE_BLOCK_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->x);
		$this->putInt($this->z);
		$this->putByte($this->y);
		$this->putByte(BlockAPI::convertHighItemIdsToOldItemIds($this->PROTOCOL, $this->block));
		$this->putByte($this->meta);
	}

}