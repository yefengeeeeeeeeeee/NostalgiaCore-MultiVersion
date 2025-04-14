<?php
class PlayerInputPacket extends RakNetDataPacket{

	public $moveStrafe, $moveForward, $isJumping, $isSneaking;

	public function encode(){
		
	}

	public function pid(){
        if($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::PLAYER_INPUT_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::PLAYER_INPUT_PACKET;
        }
		return ProtocolInfo::PLAYER_INPUT_PACKET;
	}

	public function decode()
	{
		$this->moveStrafe = $this->getFloat();
		$this->moveForward = $this->getFloat();
		$this->isJumping = $this->getByte() != 0;
		$this->isSneaking = $this->getByte() != 0;
	}
	
}

