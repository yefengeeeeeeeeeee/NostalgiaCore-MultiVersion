<?php

class BucketItem extends Item{
	private static $possiblenames = array(
		0 => "Bucket",
		1 => "Milk Bucket",
		8 => "Water Bucket",
		10 => "Lava Bucket"
	);
	public function __construct($meta = 0, $count = 1){
		parent::__construct(BUCKET, $meta, $count, "Bucket");
		$this->isActivable = true;
		$this->maxStackSize = 1;
		$this->name = BucketItem::$possiblenames[$this->meta];
	}
	
	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		if($this->meta === AIR){
			if($target instanceof LiquidBlock){
				$level->setBlock($target, new AirBlock(), true, false, true);
				if(($player->gamemode & 0x01) === 0){
					$this->meta = ($target instanceof WaterBlock) ? WATER : LAVA;
				}
				return true;
			}
		}elseif($this->meta === WATER){
			//Support Make Non-Support Water to Support Water
			if($block->getID() === AIR || ( $block instanceof WaterBlock && ($block->getMetadata() & 0x07) != 0x00 ) ){
				$water = new WaterBlock();
				$level->setBlock($block, $water, true, false, true);
				ServerAPI::request()->api->block->scheduleBlockUpdate($block, 5, BLOCK_UPDATE_NORMAL);
				//$water->place($this, $player, $block, $target, $face, $fx, $fy, $fz);
				if(($player->gamemode & 0x01) === 0){
					$this->meta = 0;
				}
				return true;
			}
		}elseif($this->meta === LAVA){
			if($block->getID() === AIR || ($block instanceof LavaBlock && ($block->getMetadata() & 0x6) != 0)){
				$lava = new LavaBlock();
				$level->setBlock($block, $lava, true, false, true);
				ServerAPI::request()->api->block->scheduleBlockUpdate($block, 40, BLOCK_UPDATE_NORMAL);
				//$lava->place(clone $this, $player, $block, $target, $face, $fx, $fy, $fz);
				if(($player->gamemode & 0x01) === 0){
					$this->meta = 0;
				}
				return true;
			}elseif($block instanceof WaterBlock){
				$level->setBlock($block, new ObsidianBlock(), true, false, true);
				if(($player->gamemode & 0x01) === 0){
					$this->meta = 0;
				}
				return true;
			}
		}
		return false;
	}
}
