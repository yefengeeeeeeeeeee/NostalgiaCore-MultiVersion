<?php

abstract class RailBaseBlock extends FlowableBlock //TODO move some methods here
{
	
	public static function isRailBlock(Level $l, $x, $y, $z){
		$id = $l->level->getBlockID($x, $y, $z);
		return $id === POWERED_RAIL || $id === RAIL;
	}
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$down = $this->getSide(0);
		if($down->getID() !== AIR and $down instanceof SolidBlock){
			
			$this->level->setBlock($block, $this, true, false, true);
			if(RailBlock::$shouldconnectrails){
				$logic = new RailLogic($this);
				$logic->place(false, true);
			}
			return true;
		}
		return false;
	}
}

