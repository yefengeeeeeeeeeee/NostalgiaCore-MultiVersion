<?php

class GravelBlock extends FallableBlock{
	public function __construct(){
		parent::__construct(GRAVEL, 0, "Gravel");
		$this->hardness = 3;
	}
	
	public function getDrops(Item $item, Player $player){
		return [
			[mt_rand(1,10) ? FLINT : GRAVEL, 0, 1],
		];
	}
	
}