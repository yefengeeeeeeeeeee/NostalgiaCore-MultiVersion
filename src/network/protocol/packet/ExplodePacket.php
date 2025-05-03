<?php

class ExplodePacket extends RakNetDataPacket{
	public $x;
	public $y;
	public $z;
	public $radius;
	public $records;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::EXPLODE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::EXPLODE_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::EXPLODE_PACKET;
		}
		return ProtocolInfo::EXPLODE_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putFloat($this->x);
		$this->putFloat($this->y);
		$this->putFloat($this->z);
		$this->putFloat($this->radius);
		$this->putInt(@count($this->records));
		if(@count($this->records) > 0){
			foreach($this->records as $record){
				if($record instanceof Vector3){
					$this->putByte($record->x);
					$this->putByte($record->y);
					$this->putByte($record->z);
				}else{
					$this->putByte($record >> 16);
					$this->putByte($record & 0xff);
					$this->putByte($record >> 8 & 0xff);
				}
			}
		}
	}

}