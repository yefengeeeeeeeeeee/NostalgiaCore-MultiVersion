<?php

class PlaceBlockPacket extends RakNetDataPacket{
    public $eid;
    public $x;
    public $z;
    public $y;
    public $block;
    public $meta;
    public $face;

    public function pid(){
        if($this->PROTOCOL < ProtocolInfo5::CURRENT_PROTOCOL_5){
            return  ProtocolInfo4::PLACE_BLOCK_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL) {
            return ProtocolInfo12::PLACE_BLOCK_PACKET;
        }
        return ProtocolInfo::PLACE_BLOCK_PACKET;
    }

    public function decode(){
        $this->eid = $this->getInt();
        $this->x = $this->getInt();
        $this->z = $this->getInt();
        $this->y = $this->getByte();
        $this->block = $this->getByte();
        $this->meta = $this->getByte();
        $this->face = $this->getByte();
    }

    public function encode(){
        $this->reset();
        $this->putInt($this->eid);
        $this->putInt($this->x);
        $this->putInt($this->z);
        $this->putByte($this->y);
        $this->putByte(BlockAPI::convertHighItemIdsToOldItemIds($this->PROTOCOL, $this->block));
        $this->putByte($this->meta);
        $this->putByte($this->face);
    }

}