<?php

class JungleWoodStairsBlock extends StairBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(JUNGLE_WOOD_STAIRS, $meta, "Jungle Wood Stairs");
	}

	public function getDrops(Item $item, Player $player){
		return [
			[$this->id, 0, 1],
		];
	}
}