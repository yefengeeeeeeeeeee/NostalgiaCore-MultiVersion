<?php

class ContainerOpenPacket extends RakNetDataPacket{
	public $windowid;
	public $type;
	public $slots;
	public $x;
	public $y;
	public $z;
    public $unknownstring;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo4::CURRENT_PROTOCOL_4){
			return  ProtocolInfo3::CONTAINER_OPEN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::CONTAINER_OPEN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::CONTAINER_OPEN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::CONTAINER_OPEN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::CONTAINER_OPEN_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::CONTAINER_OPEN_PACKET;
		}
		return ProtocolInfo::CONTAINER_OPEN_PACKET;
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putByte($this->windowid);
		$this->putByte($this->type);
		$this->putByte($this->slots);
        if ($this->PROTOCOL <= ProtocolInfo9::CURRENT_PROTOCOL_9){
            $this->putString($this->unknownstring);
            ConsoleAPI::debug($this->unknownstring);
        }else{
            $this->putInt($this->x);
            $this->putInt($this->y);
            $this->putInt($this->z);
        }
	}

}