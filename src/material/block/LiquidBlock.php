<?php

class LiquidBlock extends TransparentBlock{
	/**
	 * @param int $id
	 * @param int $meta
	 * @param string $name
	 */
	public function __construct($id, $meta = 0, $name = "Unknown"){
		parent::__construct($id, $meta, $name);
		$this->isLiquid = true;
		$this->breakable = false;
		$this->isReplaceable = true;
		$this->isSolid = false;
		$this->isFullBlock = true;
		$this->hardness = 500;
	}
	public function getDrops(Item $item, Player $player){
		return array();
	}
	
	public function getLiquidHeight(){ //TODO lava,water meta
		return (($this->meta >= 8 ? 0 : $this->meta)+1) / 9;
	}
}