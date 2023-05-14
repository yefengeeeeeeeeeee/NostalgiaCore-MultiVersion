<?php
require_once("RailBaseBlock.php");
class PoweredRailBlock extends RailBaseBlock{
	public function __construct($meta = 0){
		parent::__construct(POWERED_RAIL, 0, "PoweredRailBlock");
		$this->hardness = 0.7;
		$this->isFullBlock = false;		
		$this->isSolid = true;
	}
	
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->getID() === AIR){ //Replace with common break method TODO change meta on break
				ServerAPI::request()->api->entity->drop($this, BlockAPI::getItem($this->id, $this->meta, 1));
				$this->level->setBlock($this, new AirBlock(), true, false, true);
				return BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}
	
}