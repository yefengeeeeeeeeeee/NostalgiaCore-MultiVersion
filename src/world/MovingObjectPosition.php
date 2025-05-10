<?php

class MovingObjectPosition{

	/** 0 = block, 1 = entity, 2 = none*/
	public $typeOfHit = 2;

	public $blockX;
	public $blockY;
	public $blockZ;

	/**
	 * Which side was hit. If its -1 then it went the full length of the ray trace.
	 * Bottom = 0, Top = 1, East = 2, West = 3, North = 4, South = 5.
	 */
	public $sideHit;

	/** @var Vector3 */
	public $hitVector;

	/** @var Entity */
	public $entityHit = null;

	protected function __construct(){

	}

	public function __toString(){
		return "MovingObjectPosition: {blockXYZ: {$this->blockX} {$this->blockY} {$this->blockZ}, side: {$this->sideHit} hitVec: {$this->hitVector} entityHit: {$this->entityHit}}";
	}

	/**
	 * @param int	 $x
	 * @param int	 $y
	 * @param int	 $z
	 * @param int	 $side
	 *
	 * @return MovingObjectPosition
	 */
	public static function fromBlock($x, $y, $z, $side, Vector3 $hitVector){
		$ob = new MovingObjectPosition;
		$ob->typeOfHit = 0;
		$ob->blockX = $x;
		$ob->blockY = $y;
		$ob->blockZ = $z;
		$ob->sideHit = $side;
		$ob->hitVector = new Vector3($hitVector->x, $hitVector->y, $hitVector->z);
		return $ob;
	}

	/**
	 *
	 * @return MovingObjectPosition
	 */
	public static function fromEntity(Entity $entity){
		$ob = new MovingObjectPosition;
		$ob->typeOfHit = 1;
		$ob->entityHit = $entity;
		$ob->hitVector = new Vector3($entity->x, $entity->y, $entity->z);
		return $ob;
	}
}