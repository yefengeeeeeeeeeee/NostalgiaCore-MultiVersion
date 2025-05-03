<?php

class SetEntityDataPacket extends RakNetDataPacket{
	public $eid;
	public $metadata;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::SET_ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::SET_ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::SET_ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::SET_ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::SET_ENTITY_DATA_PACKET;
		}
		return ProtocolInfo::SET_ENTITY_DATA_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		if($this->PROTOCOL >= ProtocolInfo8::CURRENT_PROTOCOL_8)
		$this->putInt($this->eid);
		$this->put(Utils::writeMetadata($this->metadata));
	}

}