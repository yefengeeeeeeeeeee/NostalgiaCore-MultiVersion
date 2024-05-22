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
	public static $blockID = 0;
	
	public static function getDepth(Level $level, $x, $y, $z){
		[$id, $meta] = $level->level->getBlock($x, $y, $z);
		
		if($id == static::$blockID) return $meta;
		return -1;
		
	}
	
	public static function getAABB(Level $level, $x, $y, $z){
		return null;
	}
	public static function getPercentAir($meta){
		if($meta >= 8) $meta = 0;
		$f = ($meta + 1) / 9;
		return $f;
	}
	
	public static function getTickDelay(){
		throw new RuntimeException("If you see this, something bad happened");
	}
	
	public static function updateLiquid(Level $level, $x, $y, $z){
		[$id, $meta] = $level->level->getBlock($x, $y, $z);
		if($id != LAVA && $id != STILL_LAVA) continue;
		
		$zNeg = $level->level->getBlockID($x, $y, $z - 1);
		$zPos = $level->level->getBlockID($x, $y, $z + 1);
		$xNeg = $level->level->getBlockID($x - 1, $y, $z);
		$xPos = $level->level->getBlockID($x + 1, $y, $z);
		$yPos = $level->level->getBlockID($x, $y + 1, $z);
		
		if(
				$zNeg == WATER || $zNeg == STILL_WATER
			|| 	$zPos == WATER || $zPos == STILL_WATER
			||	$xNeg == WATER || $xNeg == STILL_WATER
			||	$xPos == WATER || $xPos == STILL_WATER
			||	$yPos == WATER || $yPos == STILL_WATER
		) {
			if($meta){
				//if($meta > 4) -> fizz & ret
				$replacement = COBBLESTONE;
			}else{
				$replacement = OBSIDIAN;
			}
			
			$level->fastSetBlockUpdate($x, $y, $z, $replacement, true);
		}
	}
	
	
	public function getDrops(Item $item, Player $player){
		return array();
	}
	
	public function getLiquidHeight(){
		return (($this->meta >= 8 ? 0 : $this->meta)+1) / 9;
	}
}