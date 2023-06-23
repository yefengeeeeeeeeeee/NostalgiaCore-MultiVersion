<?php

class FireBlock extends FlowableBlock implements LightingBlock{
	public function __construct($meta = 0){
		parent::__construct(FIRE, $meta, "Fire");
		$this->isReplaceable = true;
		$this->breakable = false;
		$this->isFullBlock = true;
		$this->hardness = 0;
	}
	
	public static function onRandomTick(Level $level, $x, $y, $z){
		if($level->level->getBlockID($x, $y - 1, $z) !== NETHERRACK){
			$level->setBlock(new Position($x, $y, $z, $level), new AirBlock(), true, false, true);
		}
	}
	
	public function getDrops(Item $item, Player $player){
		return array();
	}
	public function getMaxLightValue(){
		return 15;
	}
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			for($s = 0; $s <= 5; ++$s){
				$side = $this->getSide($s);
				if($side->getID() !== AIR and !($side instanceof LiquidBlock)){
					return false;
				}
			}
			$this->level->setBlock($this, new AirBlock(), true, false, true);
			return BLOCK_UPDATE_NORMAL;
		}
		return false;
	}
	
}