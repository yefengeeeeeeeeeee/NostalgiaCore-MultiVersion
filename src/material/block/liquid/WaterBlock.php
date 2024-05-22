<?php

class WaterBlock extends LiquidBlockDynamic{
	public function __construct($meta = 0){
		parent::__construct(WATER, $meta, "Water");
		$this->hardness = 500;
	}
	
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$ret = $this->level->setBlock($this, $this, true, false, true);
		//TODO place ServerAPI::request()->api->block->scheduleBlockUpdate(clone $this, 5, BLOCK_UPDATE_NORMAL);
		return $ret;
	}
	
	public static function getTickDelay(){
		return 5;
	}
	
	public static function onUpdate(Level $level, $x, $y, $z, $type){

	}
}
