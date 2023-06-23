<?php

class LeavesBlock extends TransparentBlock{
	const OAK = 0;
	const SPRUCE = 1;
	const BIRCH = 2;
	public function __construct($meta = 0){
		parent::__construct(LEAVES, $meta, "Leaves");
		$names = array(
			LeavesBlock::OAK => "Oak Leaves",
			LeavesBlock::SPRUCE => "Spruce Leaves",
			LeavesBlock::BIRCH => "Birch Leaves",
			3 => "",
		);
		$this->name = $names[$this->meta & 0x03];
		$this->hardness = 1;
	}
	public static function createIndex($x, $y, $z){
		return $x.".".$y.".".$z;
	}
	public static function findLog(Level $level, $x, $y, $z, array $visited, $distance){ //port from newest pocketmine
		$index = self::createIndex($x, $y, $z);
		if(isset($visited[$index])){
			return false;
		}
		$visited[$index] = true;

		$block = $level->getBlockWithoutVector($x, $y, $z, false);
		if($block instanceof WoodBlock){ //type doesn't matter
			return true;
		}

		if($block->getId() === LEAVES && $distance <= 4){
			if(self::findLog($level, $x - 1, $y, $z, $visited, $distance + 1)) return true;
			if(self::findLog($level, $x + 1, $y, $z, $visited, $distance + 1)) return true;
			if(self::findLog($level, $x, $y, $z - 1, $visited, $distance + 1)) return true;
			if(self::findLog($level, $x, $y, $z + 1, $visited, $distance + 1)) return true;
		}
		return false;
	}
	public static function onRandomTick(Level $level, $x, $y, $z){
		$b = $level->level->getBlock($x, $y, $z);
		$id = $b[0];
		$meta = $b[1];
		
		if(($meta & 0b00001100) === 0x08){
			$meta &= 0x03;
			$visited = array();
			if(!self::findLog($level, $x, $y, $z, $visited, 0)){
				//$this->level->setBlock($this, new AirBlock(), false, false, true);
				$level->fastSetBlockUpdate($x, $y, $z, 0, 0);
				if(mt_rand(1,20) === 1){ //Saplings
					ServerAPI::request()->api->entity->drop(new Position($x, $y, $z, $level), BlockAPI::getItem(SAPLING, $meta & 0x03, 1));
				}
				if(($meta & 0x03) === LeavesBlock::OAK and mt_rand(1,200) === 1){ //Apples
					ServerAPI::request()->api->entity->drop(new Position($x, $y, $z, $level), BlockAPI::getItem(APPLE, 0, 1));
				}
				return BLOCK_UPDATE_NORMAL;
			}
		}
	}
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			if(($this->meta & 0b00001100) === 0){
				$this->meta |= 0x08;
				$this->level->setBlock($this, $this, false, false, true);
			}
		}
		return false;
	}
	
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$this->meta |= 0x04;
		$this->level->setBlock($this, $this, true, false, true);
	}
	
	public function getDrops(Item $item, Player $player){
		$drops = array();
		if($item->isShears()){
			$drops[] = array(LEAVES, $this->meta & 0x03, 1);
		}else{
			if(mt_rand(1,20) === 1){ //Saplings
				$drops[] = array(SAPLING, $this->meta & 0x03, 1);
			}
			if(($this->meta & 0x03) === LeavesBlock::OAK and mt_rand(1,100) === 1){ //Apples
				$drops[] = array(APPLE, 0, 1);
			}
		}
		return $drops;
	}
}