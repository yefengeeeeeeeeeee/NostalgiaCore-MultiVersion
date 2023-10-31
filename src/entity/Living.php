<?php

abstract class Living extends Entity implements Damageable, Pathfindable{
	
	public static $despawnMobs, $despawnTimer, $entityPushing;
	
	public $target, $ai;
	public $pathFinder, $path = null, $currentIndex = 0, $currentNode, $pathFollower;
	public $ticksExisted = 0;
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
		$this->level->applyCallbackToNearbyEntities($this, [$this, "applyCollision"], 2); //TODO radiuses
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
	
	public function update(){
		if(self::$despawnMobs && ++$this->ticksExisted > self::$despawnTimer){
			$this->close();
			return;
		}
		if(!$this->dead && Entity::$allowedAI && $this->idleTime <= 0) {
			$this->ai->updateTasks();
		}
		$this->ai->mobController->rotateTick();
		$this->ai->mobController->movementTick();
		/*if(self::$entityPushing){
			$this->collideHandler();
		}*/
		if($this->onGround){
			//if(!$this->hasPath() && $this->pathFinder instanceof ITileNavigator){
			//	$this->path = $this->pathFinder->navigate(new PathTileXYZ($this->x, $this->y, $this->z, $this->level), new PathTileXYZ($this->x + mt_rand(-10, 10), $this->y + mt_rand(-1, 1), $this->z + mt_rand(-10, 10), $this->level), 10);
			//}
			//$this->pathFollower->followPath();
		}
		
		
		parent::update();
	}
	
	public function sendMoveUpdate(){
		if($this->counter % 3 != 0){
			return;
		}
		parent::sendMoveUpdate();
		
	}
}
