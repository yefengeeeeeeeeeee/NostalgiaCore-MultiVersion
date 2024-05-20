<?php

class RedstoneOreBlock extends SolidBlock{
	public function __construct(){
		parent::__construct(REDSTONE_ORE, 0, "Redstone Ore");
		$this->hardness = 15;
	}
	
	public static function onUpdate(Level $level, $x, $y, $z, $type){
		if($type === BLOCK_UPDATE_NORMAL || $type === BLOCK_UPDATE_TOUCH){ //TODO remove BLOCK_UPDATE_TOUCH
			$level->fastSetBlockUpdate($x, $y, $z, GLOWING_REDSTONE_ORE, 0); 
			$level->scheduleBlockUpdate(new Position($x, $y, $z, $level), Utils::getRandomUpdateTicks(), BLOCK_UPDATE_RANDOM);
		}
		return false;
	}

	public function getDrops(Item $item, Player $player){
		if($item->getPickaxeLevel() >= 4){
			return array(
				array(REDSTONE_DUST, 0, mt_rand(4, 5)),
			);
		}else{
			return array();
		}
	}
}