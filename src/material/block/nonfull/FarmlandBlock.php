<?php

class FarmlandBlock extends TransparentBlock{
	public function __construct($meta = 0){
		parent::__construct(FARMLAND, $meta, "Farmland");
		$this->hardness = 3;
	}
	public function getDrops(Item $item, Player $player){
		return array(
			array(DIRT, 0, 1),
		);
	}
	public function hasCrops(){
		//TODO vanilla 0.8.1 detection method
		$b = $this->getSide(1);
		return $b->isTransparent && $b->id != 0;
	}
	
	public static function fallOn(Level $level, $x, $y, $z, Entity $entity, $fallDistance){
		$rv = lcg_value();
		console("rv: $rv, fd: ".($fallDistance - 0.5));
		if($rv < ($fallDistance - 0.5)){
			$level->fastSetBlockUpdate($x, $y, $z, DIRT, 0);
		}
	}
	
	public static function onRandomTick(Level $level, $x, $y, $z){
		$meta = $level->level->getBlockDamage($x, $y, $z);
		$b = $level->level->getBlockID($x, $y + 1, $z);
		if(!StaticBlock::getIsFlowable($b)){
			$level->fastSetBlockUpdate($x, $y, $z, DIRT, 0, true);
		}else if($meta === 0 && mt_rand(0, 5) === 0){
			$water = self::checkWaterStatic($level, $x, $y, $z);
			if($water){
				$level->fastSetBlockUpdate($x, $y, $z, FARMLAND, 1, true);
			}elseif($b != 0 && StaticBlock::getIsTransparent($b)){
				$level->fastSetBlockUpdate($x, $y, $z, DIRT, 0, true);
			}
		}
		
		
	}
	
	public static function checkWaterStatic(Level $level, $x, $y, $z){
		for($bx = $x - 4; $bx <= $x + 4; $bx++){
			for($by = $y; $by <= $y + 1; $by++){
				for($bz = $z - 4; $bz <= $z + 4; $bz++){
					$id = $level->level->getBlockID($bx, $by, $bz);
					if($id === 8 || $id === 9){
						return true;
					}
				}
			}
		}
	}
	
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			if(!$this->getSide(1)->isTransparent){
				$this->level->setBlock($this, BlockAPI::get(DIRT, 0), true, false, true);
				return $type;
			}
		}
		return false;
	}

	public function getBlockID($x, $y, $z){
		return $this->level->level->getBlockID($x, $y, $z); //PMFLevel method
	}

	public function checkWater(){

		for($x = $this->x - 4; $x <= $this->x + 4; $x++){
			for($y = $this->y; $y <= $this->y + 1; $y++){
				for($z = $this->z - 4; $z <= $this->z + 4; $z++){
					$id = $this->getBlockID($x, $y, $z);
					if($id === 8 || $id === 9){
						return true;
					}
				}
			}
		}
		return false;

	}
}
