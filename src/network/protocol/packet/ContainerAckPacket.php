<?php

class ContainerAckPacket extends RakNetDataPacket{
	public $unknwonubyte1;
    public $unknwonubyte2; // Only exist in Version 0.3.3 && 0.4.0
	public $unknownshort;
	public $write1;
	public $write0;

	public function pid(){
		if($this->PROTOCOL < ProtocolInfo6::CURRENT_PROTOCOL_6){
			return  ProtocolInfo4::CONTAINER_ACK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
			return  ProtocolInfo6::CONTAINER_ACK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
			return  ProtocolInfo8::CONTAINER_ACK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
			return  ProtocolInfo9::CONTAINER_ACK_PACKET;
		}elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
			return  ProtocolInfo12::CONTAINER_ACK_PACKET;
		}
		return ProtocolInfo::CONTAINER_ACK_PACKET;
	}

	public function decode(){
		$this->unknwonubyte1 = $this->getByte();
		$this->unknownshort = $this->getShort();
        if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
            $this->unknwonubyte2 = $this->getByte();
            return;
        }
		$this->write1 = $this->get();
		$this->write0 = $this->get();
		ConsoleAPI::debug($this->unknwonubyte1);
		ConsoleAPI::debug($this->unknownshort);
		ConsoleAPI::debug($this->write1);
		ConsoleAPI::debug($this->write0);
	}

	public function encode(){
        if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
            $this->reset();
        }
		$this->putByte($this->unknwonubyte1);
		$this->putShort($this->unknownshort);
        if($this->PROTOCOL < ProtocolInfo8::CURRENT_PROTOCOL_8){
            $this->putByte($this->unknwonubyte2);
            return;
        }
		$this->put($this->write1);
		$this->put($this->write0);
		ConsoleAPI::debug($this->unknwonubyte1);
		ConsoleAPI::debug($this->unknownshort);
		ConsoleAPI::debug($this->write1);
		ConsoleAPI::debug($this->write0);
	}

}