<?php

class PlayerEquipmentPacket extends RakNetDataPacket{
	public $eid;
	public $item;
	public $meta;
	public $slot;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::PLAYER_EQUIPMENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
			return  ProtocolInfo4::PLAYER_EQUIPMENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
			return  ProtocolInfo5::PLAYER_EQUIPMENT_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::PLAYER_EQUIPMENT_PACKET;
		}
		return ProtocolInfo::PLAYER_EQUIPMENT_PACKET;
	}

	public function decode(){
		$this->eid = $this->getInt();
		$this->item = $this->getShort();
		$this->meta = $this->getShort();
		if($this->PROTOCOL === ProtocolInfo::CURRENT_PROTOCOL){$this->slot = $this->getSignedByte();}
	}

	public function encode(){
		$this->reset();
		$this->putInt($this->eid);
		$this->putShort(BlockAPI::convertHighItemIdsToOldItemIds($this->PROTOCOL, $this->item));
		$this->putShort($this->meta);
		if($this->PROTOCOL === ProtocolInfo::CURRENT_PROTOCOL){$this->putByte($this->slot);}
	}

}