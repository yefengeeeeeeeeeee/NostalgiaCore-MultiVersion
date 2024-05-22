<?php

class LavaBlock extends LiquidBlockDynamic implements LightingBlock{
	public function __construct($meta = 0){
		parent::__construct(LAVA, $meta, "Lava");
		$this->hardness = 500;
	}
	
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$ret = $this->level->setBlock($this, $this, true, false, true);
		//TODO place ServerAPI::request()->api->block->scheduleBlockUpdate(clone $this, 40, BLOCK_UPDATE_NORMAL);
		return $ret;
	}
	
	public static function getTickDelay(){
		return 30;
	}
	
	public function getMaxLightValue(){
		return 15;
	}
}
