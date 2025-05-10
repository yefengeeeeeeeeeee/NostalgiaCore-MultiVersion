<?php

class GlowstoneBlock extends TransparentBlock implements LightingBlock{
	public static $blockID;
	public function __construct(){
		parent::__construct(GLOWSTONE_BLOCK, 0, "Glowstone");
		$this->hardness = 1.5;
	}

	public function getMaxLightValue(){
		return 15;
	}

	public function getDrops(Item $item, Player $player){
		return [
			[GLOWSTONE_DUST, 0, mt_rand(2, 4)],
		];
	}

	public static function getCollisionBoundingBoxes(Level $level, $x, $y, $z, Entity $entity){
		return [new AxisAlignedBB($x, $y, $z, $x + 1, $y + 1, $z + 1)];
	}
}