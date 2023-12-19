<?php

class MobSpawner{
	public static $spawnAnimals = false, $spawnMobs = false;
	private $server;
	public $level;
	public static $MOB_LIMIT = 50;
	public function __construct(Level $level){
		$this->server = ServerAPI::request();
		$this->level = $level;
	}

	public function countEntities(){
		return $this->level->totalMobsAmount;
	}

	public function handle(){
		if($this->countEntities() > self::$MOB_LIMIT || count($this->level->players) <= 0){
			return false; //not spawning
		}
		return $this->spawnMobs();
	}

	public function spawnMobs(){
		$phase = $this->server->api->time->getPhase($this->level);
		if(self::$spawnAnimals && ($phase == "day" || $phase == "sunrise")){ //Animal
			$type = mt_rand(10, 13);
			$baby = false;
			$grassOnly = true;
		}elseif(self::$spawnMobs && ($phase == "night" || $phase == "sunset")){ //Monster, true night
			$type = mt_rand(32, 35);
			$grassOnly = false;
			$baby = 2;
		}else{
			return false;
		}
		$x = mt_rand(0,255);
		$z = mt_rand(0,255);
		$y = $this->getSafeY($x, $z, $grassOnly, $type >= 32 && $type <= 36 && $type != 35);
		if(!$y || $y < 0){
			return false;
		}
		$data = $this->genPosData($x, $y + 0.5, $z);
		if($baby != 2) $data["IsBaby"] = $baby;
		
		$e = $this->server->api->entity->add($this->level, 2, $type, $data);
		
		if($e instanceof Entity){
			$this->server->api->entity->spawnToAll($e);
			//console("[DEBUG] $type spawned at $x, $y, $z");
		}
		return true;
	}
	
	private function genPosData($x, $y, $z){
		return [
			"x" => $x + 0.5,
			"y" => $y,
			"z" => $z + 0.5
		];
	}
	
	protected function getSafeY($x, $z, $grassOnly = false, $highMob = false){ //first safe block //TODO check boundingbox
		$allowed = [];
		for($y = 0; $y < 128; ++$y){
			$b = $this->level->level->getBlockID($x, $y, $z);
			$b2 = $this->level->level->getBlockID($x, $y + 1, $z);
			$b1 = $this->level->level->getBlockID($x, $y - 1, $z);
			if(
				!StaticBlock::getIsSolid($b) && !StaticBlock::getIsLiquid($b) && 
				(StaticBlock::getIsSolid($b1) && ($grassOnly ? $b1 === GRASS : true) && 
				($highMob ? !StaticBlock::getIsSolid($b2) && !StaticBlock::getIsLiquid($b2) : true))
			){
				$allowed[] = $y;
			}
		}
		
		return empty($allowed) ? -1 : $allowed[mt_rand(0, count($allowed) - 1)];
	}
}

