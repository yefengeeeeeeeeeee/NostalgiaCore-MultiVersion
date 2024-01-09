<?php

abstract class Living extends Entity implements Pathfindable{
	
	public static $despawnMobs, $despawnTimer, $entityPushing = false;
	public $moveStrafing, $moveForward;
	public $target, $ai;
	public $pathFinder, $path = null, $currentIndex = 0, $currentNode, $pathFollower;
	public $ticksExisted = 0;
	
	public $landMovementFactor = 0.1; //TODO may not be constant
	public $jumpMovementFactor = 0.02; //TODO may not be constant
	public $aiMoveSpeed;
	
	
	public function __construct(Level $level, $eid, $class, $type = 0, $data = array()){
		$this->target = false;
		$this->ai = new EntityAI($this);
		$this->pathFinder = new TileNavigator(new MCBlockedProvider(), new MCDiagonalProvider(), new ManhattanHeuristic3D());
		$this->pathFollower = new PathFollower($this);
		parent::__construct($level, $eid, $class, $type, $data);
		$this->canBeAttacked = true;
		$this->hasGravity = true;
		$this->hasKnockback = true;
		//if(self::$despawnMobs) $this->server->schedule(self::$despawnTimer, [$this, "close"]); //900*20
	}
	public function fall(){
		$dmg = floor($this->fallDistance - 3);
		if($dmg > 0){
			$this->harm($dmg, "fall");
		}
	}
	
	public function getAIMoveSpeed(){
		return $this->aiMoveSpeed;
	}
	
	public function setAIMoveSpeed($speed){
		console("set spedd to $speed");
		$this->aiMoveSpeed = $speed;
		$this->moveForward = $speed;
	}
	
	public function hasPath(){
		return $this->path != null;
	}
	public function eatGrass(){}
	
	public function close(){
		parent::close();
		unset($this->pathFollower->entity);
		unset($this->ai->entity);
		unset($this->ai->mobController->entity);
		unset($this->ai);
		unset($this->parent);
	}
	
	public function canBeShot(){
		return true;
	}
	public function collideHandler(){
		$rd = ceil($this->radius);
		$minChunkX = ((int)($this->x - $rd)) >> 4;
		$minChunkZ = ((int)($this->z - $rd)) >> 4;
		$maxChunkX = ((int)($this->x + $rd)) >> 4;
		$maxChunkZ = ((int)($this->z + $rd)) >> 4;
		
		//TODO also index by chunkY?
		for($chunkX = $minChunkX; $chunkX <= $maxChunkX; ++$chunkX){
			for($chunkZ = $minChunkZ; $chunkZ <= $maxChunkZ; ++$chunkZ){
				$ind = "$chunkX $chunkZ";
				foreach($this->level->entityListPositioned[$ind] ?? [] as $entid){
					if($this->level->entityList[$entid] instanceof Entity){
						$this->applyCollision($this->level->entityList[$entid]);
					}
				}
			}
		}
	}
	
	public function applyCollision(Entity $collided){
		if($collided->boundingBox->intersectsWith($this->boundingBox) && !($this->isPlayer() && $collided->isPlayer()) && $this->eid != $collided->eid){
			$diffX = $collided->x - $this->x;
			$diffZ = $collided->z - $this->z;
			$maxDiff = max(abs($diffX), abs($diffZ));
			if($maxDiff > 0.01){
				$sqrtMax = sqrt($maxDiff);
				$diffX /= $sqrtMax;
				$diffZ /= $sqrtMax;
				
				$col = (($v = 1 / $sqrtMax) > 1 ? 1 : $v);
				$diffX *= $col;
				$diffZ *= $col;
				$diffX *= 0.05;
				$diffZ *= 0.05;
				$this->addVelocity(-$diffX, 0, -$diffZ);
				$collided->addVelocity($diffX, 0, $diffZ);
			}
		}
	}
	
	public function update($now){
		if(self::$despawnMobs && ++$this->ticksExisted > self::$despawnTimer){
			$this->close();
			return;
		}
		if($this->closed) return;

		parent::update($now);
	}
	
	public function updateEntityMovement(){
		if(!$this->dead && Entity::$allowedAI && $this->idleTime <= 0) {
			$this->ai->updateTasks();
		}
		
		$this->ai->mobController->movementTick();
		$this->ai->mobController->rotateTick();
	
		if(abs($this->speedX) < self::MIN_POSSIBLE_SPEED) $this->speedX = 0;
		if(abs($this->speedZ) < self::MIN_POSSIBLE_SPEED) $this->speedZ = 0;
		if(abs($this->speedY) < self::MIN_POSSIBLE_SPEED) $this->speedY = 0;
		
		$this->moveStrafing *= 0.98;
		$this->moveForward *= 0.98;
		$savedLandFactor = $this->landMovementFactor;
		$this->landMovementFactor *= $this->getSpeedModifer();
		$this->moveEntityWithHeading($this->moveStrafing, $this->moveForward);
		$this->landMovementFactor = $savedLandFactor;
		
		if(self::$entityPushing){
			$this->collideHandler();
		}
	}
	
	public function moveEntityWithHeading($strafe, $forward){
		//TODO lava, water
		//If not in lava in water ->
		{
			$friction = 0.91;
			
			if($this->onGround){
				$friction = 0.546;
				$blockAt = $this->level->level->getBlockID(floor($this->x), floor($this->boundingBox->minY) - 1, floor($this->z));
				
				if($blockAt > 0) $friction = StaticBlock::getSlipperiness($blockAt) * 0.91;
			}
			
			$var8 = 0.16277136 / ($friction*$friction*$friction);
			
			if($this->onGround){
				//TODO if AIEnabled
				{
					//TODO $var5 = getAIMoveSpeed
				}
				//else
				{
					$var5 = $this->landMovementFactor;
				}
				$var5 *= $var8;
			}else{
				$var5 = $this->jumpMovementFactor;
			}
			
			$this->moveFlying($strafe, $forward, $var5);
			//1.5.2 recalculates friction, might be not neccessary
			$friction = 0.91;
			
			if($this->onGround){
				$friction = 0.546;
				$blockAt = $this->level->level->getBlockID(floor($this->x), floor($this->boundingBox->minY) - 1, floor($this->z));
				
				if($blockAt > 0) $friction = StaticBlock::getSlipperiness($blockAt) * 0.91;
			}
			
			//TODO onLadder
			console($this->speedY);
			$this->move($this->speedX, $this->speedY, $this->speedZ);
			//TODO onLadder + isCollidedHorizonatlly => speedY = 0.2
			
			$this->speedY -= 0.08; //gravity
			
			$this->speedY *= 0.98;
			$this->speedX *= $friction;
			$this->speedZ *= $friction;
		}
		
		//TODO limbYaw - is it even needed?
		
	}
	
	public function sendMoveUpdate(){
		if($this->counter % 3 != 0){
			return;
		}
		parent::sendMoveUpdate();
		
	}
}
