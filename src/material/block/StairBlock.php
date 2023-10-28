<?php

class StairBlock extends TransparentBlock{
	/**
	 * @param int $id
	 * @param int $meta
	 * @param string $name
	 */
	public function __construct($id, $meta = 0, $name = "Unknown"){
		parent::__construct($id, $meta, $name);
		if(($this->meta & 0x04) === 0x04){
			$this->isFullBlock = true;
		}else{
			$this->isFullBlock = false;
		}
		$this->hardness = 30;
	}
	/*public static function getCollisionBoundingBoxes(Level $level, $x, $y, $z, Entity $entity){
		$bbs = [];
		$meta = $level->level->getBlockDamage($x, $y, $z);
		
		if(($meta & 4) != 0){
			$bbs[] = new AxisAlignedBB(0, 0.5, 0, 1, 1, 1);
		}else{
			$bbs[] = new AxisAlignedBB(0, 0, 0, 1, 0.5, 1);
		}
		
	}*/ //TODO stair collision
	/**
	 * @param Item $item
	 * @param Player $player
	 * @param Block $block
	 * @param Block $target
	 * @param int $face
	 * @param int $fx
	 * @param int $fy
	 * @param int $fz
	 *
	 * @return bool|mixed
	 */
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$faces = array(
			0 => 0,
			1 => 2,
			2 => 1,
			3 => 3,
		);
		$this->meta = $faces[$player->entity->getDirection()] & 0x03;
		if(($fy > 0.5 and $face !== 1) or $face === 0){
			$this->meta |= 0x04; //Upside-down stairs
		}
		$this->level->setBlock($block, $this, true, false, true);
		return true;
	}

	/**
	 * @param Item $item
	 * @param Player $player
	 *
	 * @return array
	 */
	public function getDrops(Item $item, Player $player){
		if($item->getPickaxeLevel() >= 1){
			return array(
				array($this->id, 0, 1),
			);
		}else{
			return array();
		}
	}
}