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
				$index = ($vBlock->x << 16) + ($vBlock->z << 8) + $vBlock->y;
				
				if(StaticBlock::getIsLiquid($blockID) && !isset($visited[$index])){
					$visited[$index] = true;
				}
				
				$blastForce -= (StaticBlock::getHardness($blockID) / 5 + 0.3) * $this->stepLen;
				if($blastForce > 0){
					if(!isset($this->affectedBlocks[$index])){
						$this->affectedBlocks[$index] = $blockID << 8 | $blockMeta;
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
			foreach($server->api->entity->getRadius($this->source, $radius+1) as $entity){
				$distance = $this->source->distance($entity);
				$distByRad = $distance / $this->size;
				if($distByRad <= 1 && $distance != 0){
					$diffX = ($entity->x - $this->source->x) / $distance;
					$diffY = ($entity->y + $entity->getEyeHeight() - $this->source->y) / $distance;
					$diffZ = ($entity->z - $this->source->z) / $distance;
					
					$impact = (1 - $distByRad) * 0.5; //TODO calculate block density around the entity instead of 0.5
					$damage = (int) (($impact * $impact + $impact) * 8 * $this->size + 1);
					if($damage > 0){
						$entity->harm($damage, "explosion");
						$entity->speedX = $diffX * $impact;
						$entity->speedY = $diffY * $impact;
						$entity->speedZ = $diffZ * $impact;
						
						if($entity->isPlayer()){
							$pk = new SetEntityMotionPacket();
							$pk->eid = 0; //XXX change
							$pk->speedX = $entity->speedX;
							$pk->speedY = $entity->speedY;
							$pk->speedZ = $entity->speedZ;
							$entity->player->dataPacket($pk);
						}
					}
				}
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
		foreach($server->api->entity->getRadius($this->source, $radius) as $entity){
			$distance = $this->source->distance($entity);
			$distByRad = $distance / $this->size;
			if($distByRad <= 1 && $distance != 0){
				$diffX = ($entity->x - $this->source->x) / $distance;
				$diffY = ($entity->y + $entity->getEyeHeight() - $this->source->y) / $distance;
				$diffZ = ($entity->z - $this->source->z) / $distance;
					
				$impact = (1 - $distByRad) * 0.5; //TODO calculate block density around the entity instead of 0.5 
				$damage = (int) (($impact * $impact + $impact) * 8 * $this->size + 1);
				if($damage > 0){
					$entity->harm($damage, "explosion");
					$entity->speedX = $diffX * $impact;
					$entity->speedY = $diffY * $impact;
					$entity->speedZ = $diffZ * $impact;
					
					if($entity->isPlayer()){
						$pk = new SetEntityMotionPacket();
						$pk->eid = 0; //XXX change
						$pk->speedX = $entity->speedX;
						$pk->speedY = $entity->speedY;
						$pk->speedZ = $entity->speedZ;
						$entity->player->dataPacket($pk);
					}
				}
			}
		}
		
		foreach($this->affectedBlocks as $xyz => $idm){
			$id = $idm >> 8 & 0xff;
			$meta = $idm & 0x0f;
			$x = $xyz >> 16;
			$z = $xyz >> 8 & 0xff;
			$y = $xyz & 0xff;
			//console("$x $y $z $id $meta");
			if($id === TNT){
				$data = [
					"x" => $x + 0.5,
					"y" => $y + 0.5,
					"z" => $z + 0.5,
					"power" => 4,
					"fuse" => mt_rand(10, 30), //0.5 to 1.5 seconds
				];
				$e = $server->api->entity->add($this->level, ENTITY_OBJECT, OBJECT_PRIMEDTNT, $data);
				$server->api->entity->spawnToAll($e);
			}elseif(mt_rand(0, 10000) < ((1 / $this->size) * 10000)){
				$block = BlockAPI::get($id, $meta, new Position($x, $y, $z, $this->level));
				$dropz = $block->getDrops($this->air, $this->nullPlayer);
				if(is_array($dropz)){
					foreach($dropz as $drop){
						$server->api->entity->drop(new Position($x + 0.5, $y, $z + 0.5, $this->level), BlockAPI::getItem($drop[0], $drop[1], $drop[2])); //id, meta, count
					}
				}
			}
			$this->level->fastSetBlockUpdate($x, $y, $z, 0, 0, true);
			$send[] = $xyz;
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
