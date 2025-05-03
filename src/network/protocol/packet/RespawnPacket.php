<?php

class RespawnPacket extends RakNetDataPacket{
	public $eid;
	public $x;
	public $y;
	public $z;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::RESPAWN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::RESPAWN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::RESPAWN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::RESPAWN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::RESPAWN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::RESPAWN_PACKET;
		}
		return ProtocolInfo::RESPAWN_PACKET;
	}

	public function decode(){
		$this->eid = $this->getInt();
		$this->x = $this->getFloat();
		$this->y = $this->getFloat();
		$this->z = $this->getFloat();
	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putFloat($this->x);
		$this->putFloat($this->y);
		$this->putFloat($this->z);
	}

}