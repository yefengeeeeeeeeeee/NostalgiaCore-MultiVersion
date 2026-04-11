<?php

class EntityDataPacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $namedtag;
	public $line1;
	public $line2;
	public $line3;
	public $line4;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9) {
			return ProtocolInfo8::ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12) {
			return ProtocolInfo9::ENTITY_DATA_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::ENTITY_DATA_PACKET;
		}
		return ProtocolInfo::ENTITY_DATA_PACKET;
	}

	public function decode(){
		$this->x = $this->getShort();
		$this->y = $this->getByte();
		$this->z = $this->getShort();
		if($this->PROTOCOL < 11){
			$this->line1 = $this->get(Utils::readLShort($this->get(2), false));
			$this->line2 = $this->get(Utils::readLShort($this->get(2), false));
			$this->line3 = $this->get(Utils::readLShort($this->get(2), false));
			$this->line4 = $this->get(Utils::readLShort($this->get(2), false));
			return;
		}elseif($this->PROTOCOL === 11){
			$this->line1 = $this->getString();
			$this->line2 = $this->getString();
			$this->line3 = $this->getString();
			$this->line4 = $this->getString();
			return;
		}
		$this->namedtag = $this->get(true);
	}

	public function encode(){
		$this->reset();
		$this->putShort($this->x);
		$this->putByte($this->y);
		$this->putShort($this->z);
		if($this->PROTOCOL < 11){
			$this->putLShort(strlen($this->line1));
			$this->put($this->line1);
			$this->putLShort(strlen($this->line2));
			$this->put($this->line2);
			$this->putLShort(strlen($this->line3));
			$this->put($this->line3);
			$this->putLShort(strlen($this->line4));
			$this->put($this->line4);
		}elseif($this->PROTOCOL === 11){
			$this->putString($this->line1);
			$this->putString($this->line2);
			$this->putString($this->line3);
			$this->putString($this->line4);
			return;
		}
		$this->put($this->namedtag);
	}

}