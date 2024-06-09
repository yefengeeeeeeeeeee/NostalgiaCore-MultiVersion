<?php

class Minecart extends Vehicle{
	const TYPE = OBJECT_MINECART;
	/**
	 * A minecart rotation matrix
	 * @var int[][][] 
	 */
	private static $matrix = [
		[
			[0, 0, -1],
			[0, 0, 1]
		],
		[
			[-1, 0, 0],
			[1, 0, 0]
		],
		[
			[-1, -1, 0],
			[1, 0, 0]
		],
		[
			[-1, 0, 0],
			[1, -1, 0]
		],
		[
			[0, 0, -1],
			[0, -1, 1]
		],
		[
			[0, -1, -1],
			[0, 0, 1]
		],
		[
			[0, 0, 1],
			[1, 0, 0]
		],
		[
			[0, 0, 1],
			[-1, 0, 0]
		],
		[
			[0, 0, -1],
			[-1, 0, 0]
		],
		[
			[0, 0, -1],
			[1, 0, 0]
		]
	];
	
	private $hurtTime = 0; //syncentdata 17 int
	private $damage = 0; //syncentdata 19 float
	
	private $minecartX = 0, $minecartY = 0, $minecartZ = 0;
	private $turnProgress = 0;
	
	
	function __construct(Level $level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->canBeAttacked = true;
		$this->x = isset($this->data["TileX"]) ? $this->data["TileX"]:$this->x;
		$this->y = isset($this->data["TileY"]) ? $this->data["TileY"]:$this->y;
		$this->z = isset($this->data["TileZ"]) ? $this->data["TileZ"]:$this->z;
		$this->setHealth(1, "generic"); //orig: 3
		$this->setSize(0.98, 0.7);
		//$this->yOffset = $this->height / 2;
	}
	
	public function isPickable(){
		return !$this->dead;
	}
	
	public function getDrops(){
		return [
			[MINECART, 0, 1]
		];
	}
	
	public function comeOffTrack($topSpeed){
		if($this->speedX < -$topSpeed) $this->speedX = -$topSpeed;
		else if($this->speedX > $topSpeed) $this->speedX = $topSpeed;
		
		if($this->speedZ < -$topSpeed) $this->speedZ = -$topSpeed;
		else if($this->speedZ > $topSpeed) $this->speedZ = $topSpeed;
		
		if($this->onGround){
			console("speedX");
			$this->speedX *= 0.5;
			$this->speedY *= 0.5;
			$this->speedZ *= 0.5;
		}
		
		$this->move($this->speedX, $this->speedY, $this->speedZ);
		
		if(!$this->onGround){
			$this->speedX *= 0.95;
			$this->speedY *= 0.95;
			$this->speedZ *= 0.95;
		}
		
	}
	public function applyCollision(Entity $collided){
		console("nya");
		$diffX = $collided->x - $this->x;
		$diffZ = $collided->z - $this->z;
		$dist = $diffX*$diffX + $diffZ*$diffZ;
		if($dist >= 0.0001){
			$sqrtMax = sqrt($dist);
			$diffX /= $sqrtMax;
			$diffZ /= $sqrtMax;
			
			$col = (($v = 1 / $sqrtMax) > 1 ? 1 : $v);
			$diffX *= $col;
			$diffZ *= $col;
			$diffX *= 0.1;
			$diffZ *= 0.1;
			
			$diffX *= 0.5;
			$diffZ *= 0.5;
			
			$this->addVelocity(-$diffX, 0, -$diffZ);
			$collided->addVelocity($diffX / 4, 0, $diffZ / 4);
		}
		//parent::applyCollision($collided);
	}
	public function update($now){
		if($this->closed === true){
			return false;
		}
		console($this->boundingBox);
		$this->updateLast();
		$this->updatePosition();
		
		$this->speedY -= 0.04;
		//TODO port stuff
		
		$this->comeOffTrack(0.4);
		
		$bb = $this->boundingBox->expand(0.2, 0, 0.2);
		$minChunkX = ((int)($bb->minX)) >> 4;
		$minChunkZ = ((int)($bb->minZ)) >> 4;
		$maxChunkX = ((int)($bb->minX)) >> 4;
		$maxChunkZ = ((int)($bb->minZ)) >> 4;
		
		//TODO also index by chunkY?
		for($chunkX = $minChunkX; $chunkX <= $maxChunkX; ++$chunkX){
			for($chunkZ = $minChunkZ; $chunkZ <= $maxChunkZ; ++$chunkZ){
				$ind = "$chunkX $chunkZ";
				foreach($this->level->entityListPositioned[$ind] ?? [] as $entid){
					$e = ($this->level->entityList[$entid] ?? null);
					if($e instanceof Entity && $e->eid != $this->eid && $e->eid != $this->linkedEntity){
						if($e->isPushable() && $this->boundingBox->intersectsWith($e->boundingBox)){
							if($e->isPlayer()){
								$this->applyCollision($e, true);
							}else{
								$e->applyCollision($this);
								console("colliding with $e");
							}
							
						}
					}
				}
			}
		}
		
		$motion = new SetEntityMotionPacket();
		$motion->eid = $e->eid;
		$motion->speedX = $e->speedX;
		$motion->speedY = $e->speedY;
		$motion->speedZ = $e->speedZ;
		foreach($this->level->players as $p){
			$p->directDataPacket(clone $motion);
		}
	}
	
	public function close()
	{
		parent::close();
		if($this->linkedEntity != 0){
			$ent = $this->level->entityList[$this->linkedEntity] ?? false;
			if($ent instanceof Entity){
				$ent->stopRiding();
			}else{
				ConsoleAPI::warn("$this is being ridden by invalid entity {$this->linkedEntity}");
			}
		}
	}
	
	public function isPushable(){
		return false; //TODO replace with true
	}
	
	public function spawn($player){
		$pk = new AddEntityPacket;
		$pk->eid = $this->eid;
		$pk->type = $this->type;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$player->dataPacket($pk);
					
		$pk = new SetEntityMotionPacket;
		$pk->eid = $this->eid;
		$pk->speedX = $this->speedX;
		$pk->speedY = $this->speedY;
		$pk->speedZ = $this->speedZ;
		$player->dataPacket($pk);
	}
	
	public function interactWith(Entity $e, $action){
		console($action);
		if($action === InteractPacket::ACTION_HOLD && $e->isPlayer() && $this->canRide($e)){
			console("set ride");
			$e->setRiding($this);
			return true;
		}
		if($action === InteractPacket::ACTION_ATTACK && $e->eid == $this->linkedEntity){
			return false; //TODO more vanilla way?
		}
		parent::interactWith($e, $action);
	}
	public function canRide($e)
	{
		return $this->linkedEntity == 0 && $e->linkedEntity == 0;
	}

}
