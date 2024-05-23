<?php

class LiquidBlockDynamic extends LiquidBlock{
	public function __construct($id, $meta = 0, $name = "Unknown"){
		parent::__construct($id, $meta, $name);
	}
	
	public static $blockID = 0;
	public static $f_d8 = 0; //TODO rename
	public static $spread = [0, 0, 0,  0];
	
	public static function getHighest(Level $level, $x, $y, $z, $highest){
		$depth = static::getDepth($level, $x, $y, $z);
		if($depth < 0) return $highest;
		if($depth == 0) ++static::$f_d8;
		if($depth >= 8) $depth = 0;
		
		return $highest >= 0 && $depth >= $highest ? $highest : $depth;
	}
	
	public static function setStatic(Level $level, $x, $y, $z){
		$meta = $level->level->getBlockDamage($x, $y, $z);
		
		$level->fastSetBlockUpdate($x, $y, $z, static::$blockID + 1, $meta);
	}
	
	public static function isWaterBlocking(Level $level, $x, $y, $z){
		$id = $level->level->getBlockID($x, $y, $z);
		
		if($id == AIR) return false;
		
		if($id == DOOR_BLOCK || $id == SIGN || $id == LADDER || $id == SUGARCANE_BLOCK) return true;
		
		if($id == CARPET || $id == SNOW_LAYER || $id == RAIL || $id == POWERED_RAIL) return false; //TODO Tile::getThickness() > 0
		
		//TODO materials
		if(!StaticBlock::getIsFlowable($id)) return 1;
		else return StaticBlock::getIsSolid($id);
	}
	
	public static function canSpreadTo(Level $level, $x, $y, $z){
		$id = $level->level->getBlockID($x, $y, $z);
		if($id == LAVA || $id == STILL_LAVA) return false;
		if((static::$blockID == WATER || static::$blockID == STILL_WATER) && ($id == WATER || $id == STILL_WATER)) return false;
		
		return static::isWaterBlocking($level, $x, $y, $z) ^ 1;
	}
	
	public static function getSpread(Level $level, $x, $y, $z){
		for($i = 0; $i < 4; ++$i){
			static::$spread[$i] = 1000;
			$xs = $x;
			$ys = $y;
			$zs = $z;
			
			switch($i){
				case 0:
					--$xs;
					break;
				case 1:
					++$xs;
					break;
				case 2:
					--$zs;
					break;
				case 3:
					++$zs;
					break;
			}
			
			if(static::isWaterBlocking($level, $xs, $ys, $zs)) continue;
			[$id, $meta] = $level->level->getBlock($xs, $ys, $zs);
			
			if(((static::$blockID == WATER && ($id == WATER || $id == STILL_WATER)) || (static::$blockID == LAVA && ($id == LAVA || $id == STILL_LAVA))) && $meta == 0){
				continue;
			}
			
			if(!static::isWaterBlocking($level, $xs, $ys - 1, $zs)) static::$spread[$i] = 0;
			else static::$spread[$i] = static::getSlopeDistance($level, $xs, $ys, $zs, 1, $i);
		}
		
		$i1 = max(static::$spread);
		$ba = [];
		for($i = 0; $i < 4; ++$i) $ba[$i] = ($i1 == static::$spread[$i]);
		return $ba;
	}
	
	public static function getSlopeDistance(Level $level, $x, $y, $z, $l, $i1){
		$j1 = 1000;
		for($k1 = 0; $k1 < 4; ++$k1){
			if($k1 == 0 && $i1 == 1 || $k1 == 1 && $i1 == 0 || $k1 == 2 &&  $i1 == 3 || $k1 == 3 && $i1 == 2){
				continue;
			}
			
			$xs = $x;
			$ys = $y;
			$zs = $z;
			
			switch($k1){
				case 0:
					--$xs;
					break;
				case 1:
					++$xs;
					break;
				case 2:
					--$zs;
					break;
				case 3:
					++$zs;
					break;
			}
			
			if(static::isWaterBlocking($level, $xs, $ys, $zs)) continue;
			[$id, $meta] = $level->level->getBlock($xs, $ys, $zs);
			
			if(
				(((static::$blockID == WATER || static::$blockID == STILL_WATER) && ($id == WATER || $id == STILL_WATER)) ||
					((static::$blockID == LAVA || static::$blockID == STILL_LAVA) && ($id == LAVA || $id == STILL_LAVA))) &&
				$meta == 0
			){
				continue;
			}
			
			if(!static::isWaterBlocking($level, $xs, $ys - 1, $zs)) return $l;
			
			if($l >= 4) continue;
			$k2 = static::getSlopeDistance($level, $xs, $ys, $zs, $l + 1, $k1);
			if($k2 < $j1) $j1 = $k2;
		}
		return $j1;
	}
	
	public static function trySpreadTo(Level $level, $x, $y, $z, $meta){
		if(static::canSpreadTo($level, $x, $y, $z)){
			$id = $level->level->getBlockID($x, $y, $z);
			
			if($id > 0){
				if(($id == LAVA || $id == STILL_LAVA) && (static::$blockID == LAVA || static::$blockID == STILL_LAVA)); //fizz
				else{
					//TODO spawnResources
				}
			}
			
			$level->fastSetBlockUpdate($x, $y, $z, static::$blockID, $meta, true);
		}
	}
	
	public static function onPlace(Level $level, $x, $y, $z){
		static::updateLiquid($level, $x, $y, $z);
		$id = $level->level->getBlockID($x, $y, $z);
		if($id == static::$blockID) ServerAPI::request()->api->block->scheduleBlockUpdateXYZ($level, $x, $y, $z, BLOCK_UPDATE_SCHEDULED, static::getTickDelay());
	}
	
	public static function onUpdate(Level $level, $x, $y, $z, $type){
		$id = $level->level->getBlockID($x, $y, $z);
		$depth = static::getDepth($level, $x, $y, $z);
		if($id == LAVA || $id == STILL_LAVA){
			$spreadAdder = 2; //XXX nether has 1
			//TODO also call trySpreadFire
		}else{
			$spreadAdder = 1;
		}
		
		$tickDelay = static::getTickDelay();
		if($depth > 0){
			static::$f_d8 = 0;
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
			
			if(static::$f_d8 && (static::$blockID == WATER)){
				//XXX Level::isSolidBlockingTile
				$idb = $level->level->getBlockID($x, $y - 1, $z);
				$metab = $level->level->getBlockDamage($x, $y, $z);
				if(StaticBlock::getIsFlowable($idb) || (($idb == WATER || $idb == STILL_WATER) && $metab != 0)){
					//$v16 = 0;
				}
			}
			
			if(static::$blockID == LAVA && $depth <= 7){
				if($v16 > 7) goto LABEL_32; //TODO remove gotos
				
				if($v16 > $depth){
					//if(mt_rand(0, 3)) $tickDelay *= 4;
				
					LABEL_30:
					if($v16 < 0){
						$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
						LABEL_33:
						$depth = $v16;
						goto LABEL_35;
					}
					LABEL_32:
					ConsoleAPI::debug("v16 is $v16");
					$level->fastSetBlockUpdateMeta($x, $y, $z, $v16, false);
					ServerAPI::request()->api->block->scheduleBlockUpdate(new Position($x, $y, $z, $level), $tickDelay); //TODO get rid of new Position
					$level->updateNeighborsAt($x, $y, $z, static::$blockID);
					goto LABEL_33;
				}
			}
			
			if($v16 != $depth) goto LABEL_30;
		}
		static::setStatic($level, $x, $y, $z);
		LABEL_35:
		if(static::canSpreadTo($level, $x, $y - 1, $z)){
			if($id == LAVA || $id == STILL_LAVA){
				$idBottom = $level->level->getBlockID($x, $y, $z);
				if($idBottom == WATER || $idBottom == STILL_WATER){
					$level->fastSetBlockUpdate($x, $y - 1, $z, STONE, 0, true);
					//fizz
				}
			}else{
				if($depth > 7){
					$meta = $depth;
				}else{
					$meta = $depth + 8;
				}
				
				$level->fastSetBlockUpdate($x, $y - 1, $z, $id, $meta, true);
			}
		}else if($depth >= 0 && ($depth == 0 || static::isWaterBlocking($level, $x, $y - 1, $z))){
			$spread = static::getSpread($level, $x, $y, $z);
			
			if($depth > 7) $v18 = 1;
			else{
				$v18 = $depth + $spreadAdder;
				if($v18 > 7) return;
			}
			if($spread[0]) static::trySpreadTo($level, $x - 1, $y, $z, $v18);
			if($spread[1]) static::trySpreadTo($level, $x + 1, $y, $z, $v18);
			
			if($spread[2]) static::trySpreadTo($level, $x, $y, $z - 1, $v18);
			if($spread[3]) static::trySpreadTo($level, $x, $y, $z + 1, $v18);
		}
		
	}
}