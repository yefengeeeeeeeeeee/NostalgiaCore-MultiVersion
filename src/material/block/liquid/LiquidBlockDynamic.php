<?php

class LiquidBlockDynamic extends LiquidBlock{
	public function __construct($id, $meta = 0, $name = "Unknown"){
		parent::__construct($id, $meta, $name);
	}
	
	public static $blockID = 0;
	public static $f_d8 = 0; //TODO rename
	
	public static function getHighest(Level $level, $x, $y, $z, $highest){
		$depth = self::getDepth($level, $x, $y, $z);
		
		if($depth >= 0){
			if($depth > 7) $depth = 0;
		}else{
			++self::$f_d8;
		}
		
		if($highest < 0) return $depth;
		else if($highest >= $depth) return $depth;
		
		return $highest;
	}
	
	public static function setStatic(Level $level, $x, $y, $z){
		$meta = $level->level->getBlockDamage($x, $y, $z);
		
		$level->fastSetBlockUpdate($x, $y, $z, self::$blockID + 1, $meta);
	}
	
	public static function onUpdate(Level $level, $x, $y, $z, $type){
		static::$blockID = $id = $level->level->getBlockID($x, $y, $z);
		$depth = static::getDepth($level, $x, $y, $z);
		if($id == LAVA || $id == STILL_LAVA){
			$spreadAdder = 2; //XXX nether has 1
			//TODO also call trySpreadFire
		}else{
			$spreadAdder = 1;
		}
		
		$tickDelay = static::getTickDelay();
		if($depth > 0){
			self::$f_d8 = 0;
			$highest = static::getHighest($level, $x - 1, $y, $z, -100);
			$highest = static::getHighest($level, $x + 1, $y, $z, $highest);
			
			$highest = static::getHighest($level, $x, $y, $z - 1, $highest);
			$highest = static::getHighest($level, $x, $y, $z + 1, $highest);
			
			if($highest + $spreadAdder > 7) $v16 = -1;
			else{
				$v16 = ($highest + $spreadAdder) & ~($highest >> 31); //XXX xor?
				if($highest < 0) $v16 = -1;
			}
			$dpth = static::getDepth($level, $x, $y + 1, $z);
			if($dpth >= 0){
				if($dpth > 7) $v16 = $dpth;
				else $v16 = $dpth + 8;
			}
			
			if(static::$f_d8 && (static::$blockID == WATER || static::$blockID == STILL_WATER)){
				//XXX Level::isSolidBlockingTile
				[$id, $meta] = $level->level->getBlock($x, $y, $z);
				if(StaticBlock::getIsFlowable($id) || ($id == WATER || $id == STILL_WATER) && $meta != 0){
					$v16 = 0;
				}
			}
			
			if((static::$blockID == LAVA || static::$blockID == STILL_LAVA) && $depth <= 7){
				if($v16 > 7) goto LABEL_32; //TODO remove gotos
				
				if($v16 > $depth){
					if(mt_rand(0, 3)) $tickDelay *= 4;
				
					LABEL_30:
					if($v16 < 0){
						$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
						LABEL_33:
						$depth = $v16;
						goto LABEL_35;
					}
					LABEL_32:
					$level->fastSetBlockUpdateMeta($x, $y, $z, $v16, false);
					ServerAPI::request()->api->block->scheduleBlockUpdate(new Position($x, $y, $z, $level), $tickDelay); //TODO get rid of new Position
					$level->updateNeighborsAt($x, $y, $z, self::$blockID);
					goto LABEL_33;
				}
			}
			
			if($v16 != $depth) goto LABEL_30;
		}
		self::setStatic($level, $x, $y, $z);
		LABEL_35:
		//TODO
		
		
	}
}