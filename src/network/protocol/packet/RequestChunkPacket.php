<?php

class RequestChunkPacket extends RakNetDataPacket{
	public $chunkX;
	public $chunkZ;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
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