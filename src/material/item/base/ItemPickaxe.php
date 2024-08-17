<?php

abstract class ItemPickaxe extends ItemTool
{
	
	public function isTool(){
		return true;
	}

	public function isPickaxe(){
		return true;
	}

	public function getLevel(){
        return match ($this->id) {
            WOODEN_PICKAXE => 1,
            GOLDEN_PICKAXE => 2,
            STONE_PICKAXE => 3,
            IRON_PICKAXE => 4,
            DIAMOND_PICKAXE => 5,
            default => false,
        };
	}
}

