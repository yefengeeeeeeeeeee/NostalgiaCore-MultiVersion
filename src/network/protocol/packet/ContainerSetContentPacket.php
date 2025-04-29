<?php

class ContainerSetContentPacket extends RakNetDataPacket{
	public $windowid;
	public $slots = [];
	public $hotbar = [];

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::CONTAINER_SET_CONTENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
			return  ProtocolInfo4::CONTAINER_SET_CONTENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
			return  ProtocolInfo5::CONTAINER_SET_CONTENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo7::CONTAINER_SET_CONTENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::CONTAINER_SET_CONTENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::CONTAINER_SET_CONTENT_PACKET;
		}
		return ProtocolInfo::CONTAINER_SET_CONTENT_PACKET;
	}

	public function decode(){
		$this->windowid = $this->getByte();
		$count = $this->getShort();
		for($s = 0; $s < $count and !$this->feof(); ++$s){
			$this->slots[$s] = $this->getSlot();
		}
		if($this->windowid === 0){
			$count = $this->getShort();
			for($s = 0; $s < $count and !$this->feof(); ++$s){
				$this->hotbar[$s] = $this->getInt();
			}
		}
	}

	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
		$this->putShort(count($this->slots));
		foreach($this->slots as $slot){
			$this->putSlot($this->PROTOCOL, $slot);
		}
		if($this->windowid === 0 and count($this->hotbar) > 0){
			$this->putShort(count($this->hotbar));//null
			foreach($this->hotbar as $slot){
				$this->putInt($slot);
			}
		}
	}

}