<?php

class ContainerAckPacket extends RakNetDataPacket{
    public $unknwonubyte1;
    public $unknownshort;
    public $write1;
    public $write0;

    public function pid(){
        if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::CONTAINER_ACK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo7::CURRENT_PROTOCOL_7){
            return  ProtocolInfo5::CONTAINER_ACK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo9::CURRENT_PROTOCOL_9){
            return  ProtocolInfo7::CONTAINER_ACK_PACKET;
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
        $this->write1 = $this->get();
        $this->write0 = $this->get();
        ConsoleAPI::debug($this->unknwonubyte1);
        ConsoleAPI::debug($this->unknownshort);
        ConsoleAPI::debug($this->write1);
        ConsoleAPI::debug($this->write0);
    }

    public function encode(){
        $this->putByte($this->unknwonubyte1);
        $this->putShort($this->unknownshort);
        $this->put($this->write1);
        $this->put($this->write0);
        ConsoleAPI::debug($this->unknwonubyte1);
        ConsoleAPI::debug($this->unknownshort);
        ConsoleAPI::debug($this->write1);
        ConsoleAPI::debug($this->write0);
    }

}