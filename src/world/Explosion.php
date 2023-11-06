<?php

class Explosion{
	
	public static $specialDrops = [
		GRASS => DIRT,
		STONE => COBBLESTONE,
		COAL_ORE => COAL,
		DIAMOND_ORE => DIAMOND,
		REDSTONE_ORE => REDSTONE,
	];
	public static $enableExplosions = true;
	public $level; //Rays
	public $source;
	public $size;
	public $affectedBlocks = [];
	public $stepLen = 0.3;
	private $rays = 16;
	private $air;
	private $nullPlayer;
	
	public function __construct(Position $center, $size){
		$this->level = $center->level;
		$this->source = $center;
		$this->size = max($size, 0);
		$this->air = BlockAPI::getItem(AIR, 0, 1);
		$this->nullPlayer = new PlayerNull();
	}
	
	public function sub_expl(&$visited, $i, $mRays, $j, $k){
		$vector = new Vector3($i / $mRays * 2 - 1, $j / $mRays * 2 - 1, $k / $mRays * 2 - 1); //($i / $mRays) * 2 - 1
		$vector = $vector->normalize()->multiply($this->stepLen);
		$pointer = clone $this->source;
		
		for($blastForce = $this->size * (mt_rand(700, 1300) / 1000); $blastForce > 0; $blastForce -= $this->stepLen * 0.75){
			$vBlock = $pointer->floor();
			$BIDM = $this->level->level->getBlock($vBlock->x, $vBlock->y, $vBlock->z);
			$blockID = $BIDM[0];
			$blockMeta = $BIDM[1];
			if($blockID > 0){
				$index = ($vBlock->x << 15) + ($vBlock->z << 7) + $vBlock->y;
				
				if(StaticBlock::getIsLiquid($blockID) && !isset($visited[$index])){
					ServerAPI::request()->api->block->scheduleBlockUpdate(new Position($vBlock->x, $vBlock->y, $vBlock->z, $this->level), 5, BLOCK_UPDATE_NORMAL);
					$visited[$index] = true;
				}
				
				$blastForce -= (StaticBlock::getHardness($blockID) / 5 + 0.3) * $this->stepLen;
				if($blastForce > 0){
					$index = ($vBlock->x << 15) + ($vBlock->z << 7) + $vBlock->y;
					if(!isset($this->affectedBlocks[$index])){
						$this->affectedBlocks[$index] = [
							"x" => $vBlock->x,
							"y" => $vBlock->y,
							"z" => $vBlock->z,
							"id" => $blockID,
							"meta" => $blockMeta
						];
					}
				}
			}
			$pointer = $pointer->add($vector);
		}
	}
	
	public function explode(){
		$radius = 2 * $this->size;
		$server = ServerAPI::request();
		if(!Explosion::$enableExplosions){ /*Disable Explosions*/
			foreach($server->api->entity->getRadius($this->source, $radius) as $entity){
				$impact = (1 - $this->source->distance($entity) / $radius) * 0.5; //pla>
				$damage = (int) (($impact * $impact + $impact) * 8 * $this->size + 1);
				$entity->harm($damage, "explosion");
			}
			$pk = new ExplodePacket; //sound fix
			$pk->x = $this->source->x;
			$pk->y = $this->source->y;
			$pk->z = $this->source->z;
			$pk->radius = $this->size;
			$pk->records = [];
			$server->api->player->broadcastPacket($this->level->players, $pk);
			return;
		}
		if($this->size < 0.1 or $server->api->dhandle("entity.explosion", [
			"level" => $this->level,
			"source" => $this->source,
			"size" => $this->size
		]) === false){
			return false;
		}
		$visited = [];
		$mRays = $this->rays - 1;
		$i = 0;
		for($j = 0; $j <= $mRays; ++$j){
			for($k = 0; $k <= $mRays; ++$k){
				$this->sub_expl($visited, $i, $mRays, $j, $k); //i wish there was #define or inline
			}
		}
		$i = $mRays;
		for($j = 0; $j <= $mRays; ++$j){
			for($k = 0; $k <= $mRays; ++$k){
				$this->sub_expl($visited, $i, $mRays, $j, $k);
			}
		}
		
		$j = 0;
		for($i = 1; $i < $mRays; ++$i){
			for($k = 0; $k <= $mRays; ++$k){
				$this->sub_expl($visited, $i, $mRays, $j, $k);
			}
		}
		
		$j = $mRays;
		for($i = 1; $i < $mRays; ++$i){
			for($k = 0; $k <= $mRays; ++$k){
				$this->sub_expl($visited, $i, $mRays, $j, $k);
			}
		}
		
		$k = 0;
		for($i = 1; $i < $mRays; ++$i){
			for($j = 1; $j < $mRays; ++$j){
				$this->sub_expl($visited, $i, $mRays, $j, $k);
			}
		}
		$k = $mRays;
		for($i = 1; $i < $mRays; ++$i){
			for($j = 1; $j < $mRays; ++$j){
				$this->sub_expl($visited, $i, $mRays, $j, $k);
			}
		}
		//if($i == 0 or $i == $mRays or $j == 0 or $j == $mRays or $k == 0 or $k == $mRays){
		
		
		$send = [];
		$source = $this->source->floor();
		foreach($server->api->entity->getRadius($this->source, $radius) as $entity){
			$impact = (1 - $this->source->distance($entity) / $radius) * 0.5; //placeholder, 0.7 should be exposure
			$damage = (int) (($impact * $impact + $impact) * 8 * $this->size + 1);
			$entity->harm($damage, "explosion");
		}
		
		foreach($this->affectedBlocks as $blockA){
			if($blockA["id"] === TNT){
				$data = [
					"x" => $blockA["x"] + 0.5,
					"y" => $blockA["y"] + 0.5,
					"z" => $blockA["z"] + 0.5,
					"power" => 4,
					"fuse" => mt_rand(10, 30), //0.5 to 1.5 seconds
				];
				$e = $server->api->entity->add($this->level, ENTITY_OBJECT, OBJECT_PRIMEDTNT, $data);
				$server->api->entity->spawnToAll($e);
			}elseif(mt_rand(0, 10000) < ((1 / $this->size) * 10000)){
				$block = BlockAPI::get($blockA["id"], $blockA["meta"], new Position($blockA["x"], $blockA["y"], $blockA["z"], $this->level));
				$dropz = $block->getDrops($this->air, $this->nullPlayer);
				if(is_array($dropz)){
					foreach($dropz as $drop){
						$server->api->entity->drop(new Position($blockA["x"] + 0.5, $blockA["y"], $blockA["z"] + 0.5, $this->level), BlockAPI::getItem($drop[0], $drop[1], $drop[2])); //id, meta, count
					}
				}
			}
			$this->level->fastSetBlockUpdate($blockA["x"], $blockA["y"], $blockA["z"], 0, 0);
			$send[] = new Vector3($blockA["x"] - $source->x, $blockA["y"] - $source->y, $blockA["z"] - $source->z);
		}
		$pk = new ExplodePacket;
		$pk->x = $this->source->x;
		$pk->y = $this->source->y;
		$pk->z = $this->source->z;
		$pk->radius = $this->size;
		$pk->records = $send;
		$server->api->player->broadcastPacket($this->level->players, $pk);
	}
}
