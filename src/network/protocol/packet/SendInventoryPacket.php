<?php

class SendInventoryPacket extends RakNetDataPacket{
	public $eid;
	public $windowid;
	public $slots = [];
	public $armor = [];

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::SEND_INVENTORY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::SEND_INVENTORY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::SEND_INVENTORY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::SEND_INVENTORY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::SEND_INVENTORY_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::SEND_INVENTORY_PACKET;
		}
		return ProtocolInfo::SEND_INVENTORY_PACKET;
	}

	public function decode(){
		$this->eid = $this->getInt();
		$this->windowid = $this->getByte();
		$count = $this->getShort();
		for($s = 0; $s < $count and !$this->feof(); ++$s){
			$this->slots[$s] = $this->getSlot();
		}
		if($this->windowid === 1){ //Armor is sent
			for($s = 0; $s < 4; ++$s){
				$this->armor[$s] = $this->getSlot();
			}
		}
	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putByte($this->windowid);
		$this->putShort(count($this->slots));
		foreach($this->slots as $slot){
			$this->putSlot($this->PROTOCOL, $slot);
		}
		if($this->windowid === 1 and count($this->armor) === 4){
			for($s = 0; $s < 4; ++$s){
				$this->putSlot($this->PROTOCOL, $this->armor[$s]);
			}
		}
	}

}