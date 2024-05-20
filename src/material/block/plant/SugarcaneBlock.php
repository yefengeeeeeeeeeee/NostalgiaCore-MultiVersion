<?php

class SugarcaneBlock extends FlowableBlock{
	public function __construct($meta = 0){
		parent::__construct(SUGARCANE_BLOCK, $meta, "Sugarcane");
		$this->isActivable = true;
		$this->hardness = 0;
	}
	
	public function getDrops(Item $item, Player $player){
		return [
			[SUGARCANE, 0, 1],
		];
	}
	public static function getAABB(Level $level, $x, $y, $z){
		return null;
	}
	public function onActivate(Item $item, Player $player){
		if($item->getID() === DYE and $item->getMetadata() === 0x0F){ //Bonemeal
			if($this->level->level->getBlockID($this->x, $this->y - 1, $this->z) != SUGARCANE_BLOCK){
				for($y = 1; $y < 3; ++$y){
					$b = $this->level->level->getBlockID($this->x, $this->y + $y, $this->z);
					if($b == AIR){
						$this->level->fastSetBlockUpdate($this->x, $this->y + $y, $this->z, SUGARCANE_BLOCK, 0, true);
					}
				}
				$this->meta = 0;
				$this->level->fastSetBlockUpdateMeta($this->x, $this->y, $this->z, $this->meta, true);
			}
			
			if(($player->gamemode & 0x01) === 0){
				$player->removeItem(DYE,0x0F,1);
			}
			return true;
		}
		return false;
	}

	public static function onUpdate(Level $level, $x, $y, $z, $type){ 
		$down = $level->level->getBlockID($x, $y - 1, $z);
		if($down == GRASS or $down == DIRT or $down == SAND){
			$b0 = $level->level->getBlockID($x, $y - 1, $z - 1);
			$b1 = $level->level->getBlockID($x, $y - 1, $z + 1);
			$b2 = $level->level->getBlockID($x - 1, $y - 1, $z);
			$b3 = $level->level->getBlockID($x + 1, $y - 1, $z);
			if($b0 != WATER && $b0 != STILL_WATER && $b1 != WATER && $b1 != STILL_WATER && $b2 != WATER && $b2 != STILL_WATER && $b3 != WATER && $b3 != STILL_WATER){
				$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
				ServerAPI::request()->api->entity->drop(new Position($x, $y, $z, $level), BlockAPI::getItem(SUGARCANE));
				return true;
			}
		}
		if(StaticBlock::getIsTransparent($down) && $down != SUGARCANE_BLOCK){ //Replace with common break method
			ServerAPI::request()->api->entity->drop(new Position($x+0.5, $y, $z+0.5, $level), BlockAPI::getItem(SUGARCANE));
			$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
			return BLOCK_UPDATE_NORMAL;
		}
		return false;
	}
	public static function onRandomTick(Level $level, $x, $y, $z){
		$aboveID = $level->level->getBlockID($x, $y + 1, $z);
		if($aboveID === AIR){
			$l = 0;
			while($level->level->getBlockId($x, $y - ++$l, $z) === REEDS);
			if($l < 3){
				$myMeta = $level->level->getBlockDamage($x, $y, $z);
				if($myMeta == 0xf){
					$level->fastSetBlockUpdate($x, $y + 1, $z, REEDS, 0);
					$level->fastSetBlockUpdateMeta($x, $y, $z, 0);
				}else{
					$level->fastSetBlockUpdateMeta($x, $y, $z, 1 + $myMeta);
				}
			}
		}
	}
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
			$down = $this->getSide(0);
			if($down->getID() === SUGARCANE_BLOCK){
				$this->level->setBlock($block, new SugarcaneBlock(), true, false, true);
				return true;
			}elseif($down->getID() === GRASS or $down->getID() === DIRT or $down->getID() === SAND){
				$block0 = $down->getSide(2);
				$block1 = $down->getSide(3);
				$block2 = $down->getSide(4);
				$block3 = $down->getSide(5);
				if(($block0 instanceof WaterBlock) or ($block1 instanceof WaterBlock) or ($block2 instanceof WaterBlock) or ($block3 instanceof WaterBlock)){
					$this->level->setBlock($block, new SugarcaneBlock(), true, false, true);
					$this->level->scheduleBlockUpdate(new Position($this, 0, 0, $this->level), Utils::getRandomUpdateTicks(), BLOCK_UPDATE_RANDOM);
					return true;
				}
			}
		return false;
	}
}
