<?php

class PlaceBlockPacket extends RakNetDataPacket{
    public $unknown1 = 0;
    public $unknown2 = 0;
    public $unknown3 = 0;
    public $unknown4 = 0;
    public $unknown5 = 0;
    public $unknown6 = 0;
    public $unknown7 = 0;

    public function pid(){
        if($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL) {
            return ProtocolInfo12::PLACE_BLOCK_PACKET;
        }
        return ProtocolInfo::PLACE_BLOCK_PACKET;
    }

    public function decode(){
        $this->unknown1 = $this->getInt();
        $this->unknown2 = $this->getInt();
        $this->unknown3 = $this->getInt();
        $this->unknown4 = $this->getByte();
        $this->unknown5 = $this->getByte();
        $this->unknown6 = $this->getByte();
        $this->unknown7 = $this->getByte();
        ConsoleAPI::info("Int1:".$this->unknown1);
        ConsoleAPI::info("Int2:".$this->unknown2);
        ConsoleAPI::info("Int3:".$this->unknown3);
        ConsoleAPI::info("Byte4:".$this->unknown4);
        ConsoleAPI::info("Byte5:".$this->unknown5);
        ConsoleAPI::info("Byte6:".$this->unknown6);
        ConsoleAPI::info("Byte7:".$this->unknown7);
    }

    public function encode(){
        $this->reset();
        $this->putInt($this->unknown1);
        $this->putInt($this->unknown2);
        $this->putInt($this->unknown3);
        $this->putByte($this->unknown4);
        $this->putByte($this->unknown5);
        $this->putByte($this->unknown6);
        $this->putByte($this->unknown7);
        ConsoleAPI::info("Int1:".$this->unknown1);
        ConsoleAPI::info("Int2:".$this->unknown2);
        ConsoleAPI::info("Int3:".$this->unknown3);
        ConsoleAPI::info("Byte4:".$this->unknown4);
        ConsoleAPI::info("Byte5:".$this->unknown5);
        ConsoleAPI::info("Byte6:".$this->unknown6);
        ConsoleAPI::info("Byte7:".$this->unknown7);
    }

}