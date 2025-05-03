<?php

class ChunkDataPacket extends RakNetDataPacket{
	public $chunkX;
	public $chunkZ;
	public $data;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::CHUNK_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::CHUNK_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::CHUNK_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::CHUNK_DATA_PACKET;
		}
		return ProtocolInfo::CHUNK_DATA_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putInt($this->chunkX);
		$this->putInt($this->chunkZ);
		$this->put($this->data);
	}

}