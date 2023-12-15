<?php

class UnloadChunkPacket extends RakNetDataPacket
{
	public $chunkX, $chunkZ;
	
	public function encode()
	{
		$this->reset();
		$this->putInt($this->chunkX);
		$this->putInt($this->chunkZ);
	}

	public function pid()
	{
		return ProtocolInfo::UNLOAD_CHUNK_PACKET;
	}

	public function decode()
	{}

}

