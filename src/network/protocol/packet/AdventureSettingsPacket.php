<?php

class AdventureSettingsPacket extends RakNetDataPacket{
	public $flags;
	
	public function pid(){
        if($this->PROTOCOL < ProtocolInfo12::CURRENT_PROTOCOL_12){
            return  ProtocolInfo9::ADVENTURE_SETTINGS_PACKET;
        }elseif($this->PROTOCOL < ProtocolInfo::CURRENT_PROTOCOL){
            return  ProtocolInfo12::ADVENTURE_SETTINGS_PACKET;
        }
		return ProtocolInfo::ADVENTURE_SETTINGS_PACKET;
	}
	
	public function decode(){

	}
	
	public function encode(){
		$this->reset();
		$this->putInt($this->flags);
	}

}