<?php

class RedMushroomBlock extends FlowableBlock{
	public function __construct(){
		parent::__construct(RED_MUSHROOM, 0, "Red Mushroom");
		$this->hardness = 0;
	}

	public static function onUpdate(Level $level, $x, $y, $z, $type){
		if(StaticBlock::getIsTransparent($level->level->getBlockID($x, $y - 1, $z))){ //Replace with common break method
			ServerAPI::request()->api->entity->drop(new Position($x+0.5, $y, $z+0.5, $level), BlockAPI::getItem(RED_MUSHROOM));
			$level->fastSetBlockUpdate($x, $y, $z, 0, 0);
			return BLOCK_UPDATE_NORMAL;
		}
		return false;
	}

	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$down = $this->getSide(0);
		if($down->isTransparent === false){
			$this->level->setBlock($block, $this, true, false, true);
			return true;
		}
		return false;
	}	
}