<?php

class ItemShovel extends ItemTool
{
	public function isShovel(){
		return true;
	}

	public function getLevel(){
		return match ($this->id) {
			WOODEN_SHOVEL => 1,
			GOLDEN_SHOVEL => 2,
			STONE_SHOVEL => 3,
			IRON_SHOVEL => 4,
			DIAMOND_SHOVEL => 5,
			default => false,
		};
	}
}

