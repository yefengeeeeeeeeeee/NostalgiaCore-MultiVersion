<?php

class PlayerActionPacket extends RakNetDataPacket{
	public $action;
	public $x;
	public $y;
	public $z;
	public $face;
	public $eid;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::PLAYER_ACTION_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::PLAYER_ACTION_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::PLAYER_ACTION_PACKET;
		}
		return ProtocolInfo::PLAYER_ACTION_PACKET;
	}

	public function decode(){
		$this->action = $this->getInt();
		$this->x = $this->getInt();
		$this->y = $this->getInt();
		$this->z = $this->getInt();
		$this->face = $this->getInt();
		$this->eid = $this->getInt();
	}

	public function encode(){

	}

}