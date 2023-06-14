<?php

class TaigaBiome extends Biome
{
	public function getTreeFeature(MersenneTwister $rand){
		if($rand->nextInt(3) == 0){
			return Feature::$PINE_TREE;
		}else{
			return Feature::$SPRUCE_TREE;
		}
	}
}

