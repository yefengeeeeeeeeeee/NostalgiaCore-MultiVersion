<?php

class InteractPacket extends RakNetDataPacket{
	const ACTION_HOLD = 1; //HOLD CLICK ON ENTITY
	const ACTION_ATTACK = 2; //SIMPLE CLICK(ATTACK)
	const ACTION_VEHICLE_EXIT = 3; //EXIT FROM ENTITY(MINECART)
	public $action;
	public $eid;
	public $target;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::INTERACT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
			return  ProtocolInfo4::INTERACT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
			return  ProtocolInfo5::INTERACT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo7::INTERACT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::INTERACT_PACKET;
		}
		return ProtocolInfo::INTERACT_PACKET;
	}

	public function decode(){
		$this->action = $this->getByte();
		$this->eid = $this->getInt();
		$this->target = $this->getInt();
	}

	public function encode(){
		$this->reset();
		$this->putByte($this->action);
		$this->putInt($this->eid);
		$this->putInt($this->target);
	}

}