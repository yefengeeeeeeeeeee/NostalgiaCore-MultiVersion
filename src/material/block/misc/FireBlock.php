<?php

class FireBlock extends FlowableBlock implements LightingBlock{
	
	public static $flammability = [];
	public static $fireCatchingChance = [];
	
	public function __construct($meta = 0){
		parent::__construct(FIRE, $meta, "Fire");
		$this->isReplaceable = true;
		$this->breakable = false;
		$this->isFullBlock = true;
		$this->hardness = 0;
	}
	public static function getAABB(Level $level, $x, $y, $z){
		return null;
	}
	public static function setFlammabilityAndCatchingChance($blockID, $flammability, $v){
		self::$flammability[$blockID] = $flammability;
		self::$fireCatchingChance[$blockID] = $v;
	}
	
	public static function canBurn(Level $level, $x, $y, $z){
		return self::$flammability[$level->level->getBlockID($x, $y, $z)] > 0;
	}
	
	public static function onRandomTick(Level $level, $x, $y, $z){
		if($level->level->getBlockID($x, $y - 1, $z) !== NETHERRACK){
			$level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
		}
	}
	
	public function getDrops(Item $item, Player $player){
		return array();
	}
	public function getMaxLightValue(){
		return 15;
	}
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			for($s = 0; $s <= 5; ++$s){
				$side = $this->getSide($s);
				if($side->getID() !== AIR and !($side instanceof LiquidBlock)){
					return false;
				}
			}
			$this->level->setBlock($this, new AirBlock(), true, false, true);
			return BLOCK_UPDATE_NORMAL;
		}else if($type === BLOCK_UPDATE_SCHEDULED){
			$idBelow = $this->level->level->getBlockID($this->x, $this->y - 1, $this->z);
			$alwaysBurn = $idBelow == NETHERRACK;
			
			if($this->meta < 15){
				$newMeta = $this->meta + 1; //TODO better formula
				if($newMeta > 15) $newMeta = 15;
				$this->level->fastSetBlockUpdate($this->x, $this->y, $this->z, $this->id, $newMeta);
			}
			if($this->meta == 15){
				if(!$alwaysBurn && !self::canBurn($this->level, $this->x, $this->y - 1, $this->z) && mt_rand(0, 4) == 0){
					REMOVE_FIRE:
					$this->level->fastSetBlockUpdate($this->x, $this->y, $this->z, 0, 0);
					return false;
				}
			}
			$chance = self::$fireCatchingChance[$idBelow];
				
			if(mt_rand(0, 249) < $chance){
				//TODO ignite tnt
				$this->level->fastSetBlockUpdate($this->x, $this->y - 1, $this->z, 0, 0, true);
				goto REMOVE_FIRE;
			}
			
			$this->level->scheduleBlockUpdate($this, 30, BLOCK_UPDATE_SCHEDULED); //TODO looks like it also adds mt_rand(0, 9) to it
		}
		return false;
	}
	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		parent::place($item, $player, $block, $target, $face, $fx, $fy, $fz);
		$this->level->scheduleBlockUpdate($this, 30, BLOCK_UPDATE_SCHEDULED);
		
		
	}
}