<?php
/**
 * Small class to hopefully optimize entities a bit
 */
class StaticBlock
{
	const DEFAULT_SLIPPERINESS = 0.6;
	private static $NULL_BOUNDS;
	public static $isSolid = [];
	public static $isTransparent = [];
	public static $isFlowable = [];
	public static $isReplaceable = [];
	public static $isPlaceable = [];
	public static $hasPhysics = [];
	public static $isLiquid = [];
	public static $isFullBlock = [];
	
	public static $slipperiness = [];
	public static $boundingBoxes = [];
	public static function init(){
		self::$NULL_BOUNDS = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
		foreach(Block::$class as $nonstaticname){
			/**@var Block $b*/
			$b = new $nonstaticname();
			
			self::$isSolid[$b->getID()] = $b->isSolid;
			self::$isTransparent[$b->getID()] = $b->isTransparent;
			self::$isFlowable[$b->getID()] = $b->isFlowable;
			self::$isReplaceable[$b->getID()] = $b->isReplaceable;
			self::$isPlaceable[$b->getID()] = $b->isPlaceable;
			self::$hasPhysics[$b->getID()] = $b->hasPhysics;
			self::$isLiquid[$b->getID()] = $b->isLiquid;
			self::$isFullBlock[$b->getID()] = $b->isFullBlock;
			
			self::$slipperiness[$b->getID()] = $b->slipperiness;
			self::$boundingBoxes[$b->getID()] = $b->boundingBox;
		}
	}
	
	public static function getBoundingBoxForBlockCoords($id, $x, $y, $z){
		/**@var AxisAlignedBB $bb*/
		$bb = nullsafe(self::$boundingBoxes[$id], false);
		if($bb === false){
			return clone self::$NULL_BOUNDS;
		}
		$bb = clone $bb;
		return $bb->setBounds($x + $bb->minX, $y + $bb->minY, $z + $bb->minZ, $x + $bb->maxX, $y + $bb->maxY, $z + $bb->maxZ);
	}
	
	
	public static function getSlipperiness($id){
		return nullsafe(self::$slipperiness[$id], StaticBlock::DEFAULT_SLIPPERINESS);
	}
	
	public static function getIsSolid($id){
		return nullsafe(self::$isSolid[$id], false);
	}
	
	public static function getIsTransparent($id){
		return nullsafe(self::$isTransparent[$id], false);
	}
	
	public static function getIsFlowable($id){
		return nullsafe(self::$isFlowable[$id], false);
	}
	
	public static function getIsReplaceable($id){
		return nullsafe(self::$isReplaceable[$id], false);
	}
	
	public static function getIsPlaceable($id){
		return nullsafe(self::$isPlaceable[$id], false);
	}
	
	public static function getHasPhysics($id){
		return nullsafe(self::$hasPhysics[$id], false);
	}
	
	public static function getIsLiquid($id){
		return nullsafe(self::$isLiquid[$id], false);
	}
	
	public static function getIsFullBlock($id){
		return nullsafe(self::$isFullBlock[$id], false);
	}
}

