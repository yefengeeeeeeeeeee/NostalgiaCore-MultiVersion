<?php

abstract class ItemHoe extends ItemTool
{
	public function isHoe(){
		return true;
	}
	
	public function useOn($object, $force = false){
		if(($object instanceof Block) and ($object->getID() === GRASS or $object->getID() === DIRT)){
			$this->meta++;
			return true;
		}else{
			return parent::useOn($object, $force);
		}
	}

	public function getLevel(){
		return match ($this->id) {
			WOODEN_HOE => 1,
			GOLDEN_HOE => 2,
			STONE_HOE => 3,
			IRON_HOE => 4,
			DIAMOND_HOE => 5,
			default => false,
		};
	}
}

