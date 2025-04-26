<?php

class RequestChunkPacket extends RakNetDataPacket{
	public $chunkX;
	public $chunkZ;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
            return  ProtocolInfo3::REQUEST_CHUNK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::REQUEST_CHUNK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::REQUEST_CHUNK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::REQUEST_CHUNK_PACKET;
        }
		return ProtocolInfo::REQUEST_CHUNK_PACKET;
	}
	
	public function decode(){
		$this->chunkX = $this->getInt();
		$this->chunkZ = $this->getInt();
	}
	
	public function encode(){

	}

}