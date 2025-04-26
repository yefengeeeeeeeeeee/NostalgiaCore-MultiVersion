<?php

class AnimatePacket extends RakNetDataPacket{
	const ANIM_SWING_HAND = 0x1;
	const ANIM_STOP_SLEEP = 0x3;
	
	public $action;
	public $eid;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::ANIMATE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::ANIMATE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::ANIMATE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::ANIMATE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::ANIMATE_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::ANIMATE_PACKET;
        }
		return ProtocolInfo::ANIMATE_PACKET;
	}
	
	public function decode(){
		$this->action = $this->getByte();
		$this->eid = $this->getInt();
	}
	
	public function encode(){
		$this->reset();
		$this->putByte($this->action);
		$this->putInt($this->eid);
	}

}