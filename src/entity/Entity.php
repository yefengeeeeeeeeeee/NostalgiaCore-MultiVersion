<?php

class Entity extends Position
{

	const TYPE = - 1;
	const CLASS_TYPE = - 1;
	const MIN_POSSIBLE_SPEED = 1/8000; //anything below will send 0 to player
	public $counter = 0;
	public $fallDistance = 0;
	public static $updateOnTick, $allowedAI;
	public $isCollidable;
	public $canBeAttacked;
	public $moveTime, $lookTime, $idleTime, $knockbackTime = 0;
	public $needsUpdate = true;
	public $speedModifer;
	public $hasGravity;
	/**
	 * @var AxisAlignedBB
	 */
	public $boundingBox;
	public $age;
	public $air;
	public $maxAir = 300;
	public $spawntime;
	public $dmgcounter;
	public $eid;
	public $type;
	public $name;
	public $delayBeforePickup;
	public $x, $y, $z;
	public $speedX, $speedY, $speedZ, $speed;
	public $lastX = 0, $lastY  = 0, $lastZ  = 0, $lastYaw  = 0, $lastPitch  = 0, $lastTime = 0, $lastHeadYaw = 0, $lastSpeedX = 0, $lastSpeedY = 0, $lastSpeedZ = 0;
	/**
	 * 0 = lastX, 
	 * 1 = lastY, 
	 * 2 = lastZ, 
	 * 3 = lastYaw, 
	 * 4 = lastPitch, 
	 * 5 = lastTime. 
	 * 
	 * It is not recommended to use this and it is left as a backwards compability.
	 * 
	 * @var array
	 */
	public $last;
	public $yaw, $headYaw;
	public $pitch;
	public $dead;
	public $data;
	public $class;
	public $attach;
	public $closed;
	/**
	 * @var Player
	 */
	public $player;
	public $fallY;
	public $fallStart;
	private $state;
	private $tickCounter;
	private $speedMeasure = array(0, 0, 0, 0, 0, 0, 0);
	public $server;
	private $isStatic;
	public $level;
	public $isRiding = false;
	public $lastUpdate;
	public $linkedEntity = null;
	public $check = true;
	public $width = 1;
	public $height = 1;
	public $random;
	public $radius;
	public $inAction = false;
	public $inActionCounter = 0;
	public $hasKnockback;
	public $hasJumped;
	public $invincible, $crouched, $fire, $health, $status;
	public $position;
	public $onGround, $inWater;
	public $carryoverDamage;
	public $gravity;
	
	public $stepHeight = 0.5;
	public $enableAutojump = false;
	function __construct(Level $level, $eid, $class, $type = 0, $data = array())
	{
		$this->random = new Random();
		$this->last = [&$this->lastX, &$this->lastY, &$this->lastZ, &$this->lastYaw, &$this->lastPitch, &$this->lastTime]; //pointers to variables
		$this->canBeAttacked = false;
		$this->hasKnockback = false;
		$this->level = $level;
		$this->speedModifer = 0.7;
		$this->fallY = false;
		$this->fallStart = false;
		$this->server = ServerAPI::request();
		$this->eid = (int) $eid;
		$this->type = (int) $type;
		$this->class = (int) $class;
		$this->player = false;
		$this->attach = false;
		$this->data = $data;
		$this->status = 0;
		$this->health = 20;
		$this->hasGravity = false;
		$this->dmgcounter = array(0, 0, 0);
		$this->air = $this->maxAir;
		$this->fire = 0;
		$this->crouched = false;
		$this->invincible = false;
		$this->lastUpdate = $this->spawntime = microtime(true);
		$this->dead = false;
		$this->closed = false;
		$this->isStatic = false;
		$this->name = "";
		$this->gravity = 0.08;
		$this->state = $this->data["State"] = isset($this->data["State"]) ? $this->data["State"] : 0;
		$this->tickCounter = 0;
		$this->server->query("INSERT OR REPLACE INTO entities (EID, level, type, class, health, hasUpdate) VALUES (" . $this->eid . ", '" . $this->level->getName() . "', " . $this->type . ", " . $this->class . ", " . $this->health . ", 0);");
		$this->x = isset($this->data["x"]) ? (float) $this->data["x"] : 0;
		$this->y = isset($this->data["y"]) ? (float) $this->data["y"] : 0;
		$this->z = isset($this->data["z"]) ? (float) $this->data["z"] : 0;
		$this->speedX = isset($this->data["speedX"]) ? (float) $this->data["speedX"] : 0;
		$this->speedY = isset($this->data["speedY"]) ? (float) $this->data["speedY"] : 0;
		$this->speedZ = isset($this->data["speedZ"]) ? (float) $this->data["speedZ"] : 0;
		$this->speed = 0;
		$this->yaw = isset($this->data["yaw"]) ? (float) $this->data["yaw"] : 0;
		$this->headYaw = isset($this->data["headYaw"]) ? $this->data["headYaw"] : $this->yaw;
		$this->pitch = isset($this->data["pitch"]) ? (float) $this->data["pitch"] : 0;
		$this->position = array(
			"level" => $this->level,
			"x" => &$this->x,
			"y" => &$this->y,
			"z" => &$this->z,
			"yaw" => &$this->yaw,
			"pitch" => &$this->pitch
		);
		$this->moveTime = 0;
		$this->lookTime = 0;
		$this->onGround = false;
		switch($this->class) {
			case ENTITY_PLAYER:
				$this->player = $this->data["player"];
				$this->setHealth($this->health, "generic");
				$this->speedModifer = 1;
				$this->width = 0.6;
				$this->height = 1.8;
				$this->hasKnockback = true;
				$this->hasGravity = true;
				$this->canBeAttacked = true;
				break;
			case ENTITY_OBJECT:
				$this->x = isset($this->data["TileX"]) ? $this->data["TileX"] : $this->x;
				$this->y = isset($this->data["TileY"]) ? $this->data["TileY"] : $this->y;
				$this->z = isset($this->data["TileZ"]) ? $this->data["TileZ"] : $this->z;
				$this->setHealth(1, "generic");
				$this->stepHeight = false;

				$this->width = 1;
				$this->height = 1;
				if($this->type === OBJECT_SNOWBALL){
					$this->server->schedule(1210, array(
						$this,
						"update"
					));
				}
				break;
		}
		$this->radius = $this->width / 2;
		$this->boundingBox = new AxisAlignedBB($this->x - $this->radius, $this->y, $this->z - $this->radius, $this->x + $this->radius, $this->y + $this->height, $this->z + $this->radius);
		$this->updateLast();
		$this->updatePosition();
		if($this->isInVoid()){
			$this->outOfWorld();
		}
	}
	
	public function isType(){
		return in_array($this->type, func_get_args());
	}
	
	public function attackEntity($entity){

	}
	
	public function addVelocity($vX, $vY = 0, $vZ = 0)
	{
		if($vX instanceof Vector3){
			return $this->addVelocity($vX->x, $vX->y, $vX->z);
		}
		$this->speedX += $vX;
		$this->speedY += $vY;
		$this->speedZ += $vZ;
	}
	
	public function isMovingHorizontally()
	{
		return ($this->speedX >= self::MIN_POSSIBLE_SPEED || $this->speedX <= -self::MIN_POSSIBLE_SPEED) || ($this->speedZ >= self::MIN_POSSIBLE_SPEED || $this->speedZ <= -self::MIN_POSSIBLE_SPEED);
	}
	public function isMoving()
	{
		return  $this->isMovingHorizontally() || ($this->speedY > self::MIN_POSSIBLE_SPEED || $this->speedY < -self::MIN_POSSIBLE_SPEED);
	}

	public function setVelocity($vX, $vY = 0, $vZ = 0)
	{
		if($vX instanceof Vector3){
			return $this->setVelocity($vX->x, $vX->y, $vX->z);
		}
		$this->speedX = $vX;
		$this->speedY = $vY;
		$this->speedZ = $vZ;
	}

	/**
	 *
	 * @param mixed $e
	 *			Entity instance or EID
	 * @return string|false if failed
	 */
	public static function getNameOf($e)
	{
		if($e instanceof Entity){
			return $e->getName();
		} elseif(($e = ServerAPI::request()->api->entity->get($e)) != false){
			return $e->getName();
		}
		return false;
	}

	/**
	 *
	 * @param mixed $e
	 *			Entity instance or EID
	 * @return number|false if failed
	 */
	public static function getHeightOf($e)
	{
		if($e instanceof Entity){
			return $e->getHeight();
		} elseif(($e = ServerAPI::request()->api->entity->get($e)) != false){
			return $e->getHeight();
		}
		return false;
	}

	/**
	 *
	 * @param mixed $e
	 *			Entity instance or EID
	 * @return number|false if failed
	 */
	public static function getWidthOf($e)
	{
		if($e instanceof Entity){
			return $e->getWidth();
		} elseif(($e = ServerAPI::request()->api->entity->get($e)) != false){
			return $e->getWidth();
		}
		return false;
	}

	/**
	 * Get an entity height
	 *
	 * @return number
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get an entity width
	 *
	 * @return number
	 */
	public function getWidth()
	{
		return $this->width;
	}

	public function getDrops()
	{
		if($this->class === ENTITY_PLAYER and $this->player instanceof Player and ($this->player->gamemode & 0x01) === 0){
			$inv = [];
			for($i = 0; $i < PLAYER_SURVIVAL_SLOTS; ++ $i){
				$slot = $this->player->getSlot($i);
				$this->player->setSlot($i, BlockAPI::getItem(AIR, 0, 0));
				if($slot->getID() !== AIR and $slot->count > 0){
					$inv[] = array(
						$slot->getID(),
						$slot->getMetadata(),
						$slot->count
					);
				}
			}
			for($re = 0; $re < 4; $re ++){
				$slot = $this->player->getArmor($re);
				$this->player->setArmor($re, BlockAPI::getItem(AIR, 0, 0));
				if($slot->getID() !== AIR and $slot->count > 0){
					$inv[] = array(
						$slot->getID(),
						$slot->getMetadata(),
						$slot->count
					);
				}
			}
			return $inv;
		}
		return [];
	}

	private function spawnDrops()
	{
		foreach($this->getDrops() as $drop){
			$this->server->api->entity->drop($this, BlockAPI::getItem($drop[0] & 0xFFFF, $drop[1] & 0xFFFF, $drop[2] & 0xFF), true);
		}
	}

	public function environmentUpdate()
	{
		$hasUpdate = $this->class === ENTITY_MOB; // force true for mobs
		$time = microtime(true);
		if($this->class === ENTITY_PLAYER and ($this->player instanceof Player) and $this->player->spawned === true and $this->player->blocked !== true && ! $this->dead){
			$myBB = $this->boundingBox->grow(1, 0.5, 1);
			foreach($this->server->api->entity->getRadius($this, 2, ENTITY_ITEM) as $item){
				if(!$item->closed && $item->spawntime > 0 && $item->delayBeforePickup == 0){
					if($item->boundingBox->intersectsWith($myBB)){ 
						if((($this->player->gamemode & 0x01) === 1 || $this->player->hasSpace($item->type, $item->meta, $item->stack) === true) && $this->server->api->dhandle("player.pickup", array(
							"eid" => $this->player->eid,
							"player" => $this->player,
							"entity" => $item,
							"block" => $item->type,
							"meta" => $item->meta,
							"target" => $item->eid
						)) !== false){
							$item->close();
						}
					}
					
				}
			}
			unset($myBB);
		} elseif($this->class === ENTITY_ITEM){
			if(($time - $this->spawntime) >= 300){
				$this->close(); // Despawn timer
				return false;
			}
		} elseif($this->class === ENTITY_OBJECT and $this->type === OBJECT_SNOWBALL){
			if(($time - $this->spawntime) >= 60){
				$this->close();
				return false;
			}
		}

		if($this->class !== ENTITY_PLAYER and ($this->x <= 0 or $this->z <= 0 or $this->x >= 256 or $this->z >= 256 or $this->y >= 128 or $this->y <= 0)){
			$this->close();
			return false;
		}

		if($this->dead === true){
			$this->fire = 0;
			$this->air = $this->maxAir;
			return false;
		}
		if($this->isInVoid()){
			$this->outOfWorld();
			$hasUpdate = true;
		}

		if($this->fire > 0){
			if(($this->fire % 20) === 0){
				$this->harm(1, "burning");
			}
			$this->fire -= 10;
			if($this->fire <= 0){
				$this->fire = 0;
				$this->updateMetadata();
			} else{
				$hasUpdate = true;
			}
		}

		$startX = floor($this->boundingBox->minX);
		$startY = floor($this->boundingBox->minY);
		$startZ = floor($this->boundingBox->minZ);
		$endX = ceil($this->boundingBox->maxX);
		$endY = ceil($this->boundingBox->maxY);
		$endZ = ceil($this->boundingBox->maxZ);
		
		if(!($this instanceof Painting) && !($this->isPlayer() && $this->player->isSleeping !== false)){ //TODO better way to fix
			for($i = 0; $i < 8; ++$i){
				$x = ((($i >> 0) % 2) - 0.5) * $this->width * 0.8;
				$y= ((($i >> 1) % 2) - 0.5) * 0.1;
				$z = ((($i >> 2) % 2) - 0.5) * $this->width * 0.8;
				
				$blockX = (int) ($this->x + $x);
				$blockY = (int) ($this->y + $this->getEyeHeight() + $y);
				$blockZ = (int) ($this->z + $z);
				
				if(!StaticBlock::getIsTransparent($this->level->level->getBlockID($blockX, $blockY, $blockZ))){
					$this->harm(1, "suffocation"); // Suffocation
					$hasUpdate = true;
					break;
				}
			}
		}
		
		//air damage
		if($this->isPlayer() || $this instanceof Living){
			$d = $this->y + $this->getEyeHeight();
			$x = floor($this->x);
			$y = floor($d);
			$z = floor($this->z);
			
			$id = $this->level->level->getBlockID($x, $y, $z);
			if($id == WATER || $id == STILL_WATER){
				$f = LiquidBlock::getPercentAir($this->level->level->getBlockDamage($x, $y, $z)) - 0.1111111;
				$f1 = ($y + 1) - $f;
				if($d < $f1){
					--$this->air;
					if($this->air <= 0){
						$this->harm(2, "water");
					}
				}else{
					$this->air = $this->maxAir;
				}
			}else{
				$this->air = $this->maxAir;
			}
		}
		
		
		for ($y = $startY; $y <= $endY; ++$y){
			for ($x = $startX; $x <= $endX; ++$x){
				for ($z = $startZ; $z <= $endZ; ++$z){
					$b = $this->level->level->getBlock($x, $y, $z);
					$id = $b[0];
					$meta = $b[1];
					switch ($id) {
						case WATER:
						case STILL_WATER: // Drowing
							if ($this->fire > 0 and $this->inBlock(new Vector3($x, $y, $z))) {
								$this->fire = 0;
								$this->updateMetadata();
							}
							break;
						case LAVA: // Lava damage
						case STILL_LAVA:
							if ($this->inBlock(new Vector3($x, $y, $z))) {
								$this->harm(5, "lava");
								$this->fire = 300;
								if($this->isPlayer() and ($this->player->gamemode & 0x01) === CREATIVE){
									$this->fire = 1;
								}
								$this->updateMetadata();
								$hasUpdate = true;
							}
							break;
						case FIRE: // Fire block damage
							if ($this->inBlock(new Vector3($x, $y, $z))) {
								$this->harm(1, "fire");
								$this->fire = 300;
								if($this->isPlayer() and ($this->player->gamemode & 0x01) === CREATIVE){
									$this->fire = 1;
								}
								$this->updateMetadata();
								$hasUpdate = true;
							}
							break;
						case CACTUS: // Cactus damage
							if ((new AxisAlignedBB($x, $y, $z, $x + 1, $y + 1, $z + 1))->intersectsWith($this->boundingBox)) {
								$this->harm(1, "cactus");
								$hasUpdate = true;
							}
							break;
						default:
							break;
					}
				}
			}
		}
		return $hasUpdate;
	}
	
	public function updateEntityMovement(){}


	public function isInVoid(){
		return $this->y < -1.6;
	}
	
	public function update(){
		if($this->closed === true){
			return false;
		}
		
		$now = microtime(true);
		if($this->check === false){
			$this->lastUpdate = $now;
			return;
		}
		
		$hasUpdate = $this->environmentUpdate();

		if($this->closed === true){
			return false;
		}
		++$this->counter;
		if($this->isStatic === false){
			if(!$this->isPlayer()){
				$this->updateLast();
				$this->updateEntityMovement();
				$update = false;
				if($this->speedX > -self::MIN_POSSIBLE_SPEED && $this->speedX < self::MIN_POSSIBLE_SPEED){
					$this->speedX = 0;
				}
				if($this->speedZ > -self::MIN_POSSIBLE_SPEED && $this->speedZ < self::MIN_POSSIBLE_SPEED){
					$this->speedZ = 0;
				}
				if($this->speedY > -self::MIN_POSSIBLE_SPEED && $this->speedY < self::MIN_POSSIBLE_SPEED){
					$this->speedY = 0;
				}
				
				$horizontalMultiplyFactor = 0.91;
				if($this->onGround){
					$horizontalMultiplyFactor = 0.54;
					$b = $this->level->level->getBlockID(floor($this->x), floor($this->boundingBox->minX) - 1, floor($this->z));
					if($b > 0){
						$horizontalMultiplyFactor = StaticBlock::getSlipperiness($b) * 0.91;
					}
				}
				
				if($this->inWater){
					$this->speedX *= 0.8;
					$this->speedY *= 0.8;
					$this->speedZ *= 0.8;
				}else{
					$this->speedX *= $horizontalMultiplyFactor;
					$this->speedY *= 0.98;
					$this->speedZ *= $horizontalMultiplyFactor;
				}
				
				$savedSpeedY = $this->speedY;
				$savedSpeedX = $this->speedX;
				$savedSpeedZ = $this->speedZ;
				$ultraMegaOldBB = clone $this->boundingBox;
				$stepSuccess = false;
				$beforeStepSpeedY = 0;
				if($this->class === ENTITY_MOB || $this->class === ENTITY_ITEM || ($this->class === ENTITY_OBJECT && $this->type === OBJECT_PRIMEDTNT) || $this->class === ENTITY_FALLING){
					$water = false;
					if($this->hasGravity){
						$this->speedY -= $this->inWater ? 0.02 : ($this->gravity);
					}
					$savedSpeedY = $this->speedY;
					$aABB = $this->boundingBox->addCoord($this->speedX, $this->speedY, $this->speedZ);
					$x0 = floor($aABB->minX);
					$x1 = ceil($aABB->maxX);
					$y0 = floor($aABB->minY);
					$y1 = ceil($aABB->maxY);
					$z0 = floor($aABB->minZ);
					$z1 = ceil($aABB->maxZ);
					
					for($x = $x0; $x <= $x1; ++$x){
						for($y = $y0; $y <= $y1; ++$y){
							for($z = $z0; $z <= $z1; ++$z){
								$b = $this->level->level->getBlockID($x, $y, $z);
								if(($b === WATER || $b === STILL_WATER)){
									$water = $y == ($y1 - 1);
									$this->fallDistance = 0;
								}
								if($b != 0){
									$blockBounds = Block::$class[$b]::getCollisionBoundingBoxes($this->level, $x, $y, $z, $this);
									foreach($blockBounds as $blockBound){
										$this->speedY = $blockBound->calculateYOffset($this->boundingBox, $this->speedY);
										$this->speedX = $blockBound->calculateXOffset($this->boundingBox, $this->speedX);
										$this->speedZ = $blockBound->calculateZOffset($this->boundingBox, $this->speedZ);
									}
								}
							}
						}
					}
					$this->boundingBox->offset($this->speedX, $this->speedY, $this->speedZ);
					$this->inWater = $water;
					$fallingFlag = $this->onGround || $savedSpeedY != $this->speedY && $savedSpeedY < 0.0;
					$beforeStepSpeedY = $this->speedY;
					
					if($this->stepHeight > 0 && $fallingFlag && ($this->speedX != $savedSpeedX || $this->speedZ != $savedSpeedZ)){
						$cx = $this->speedX;
						$cy = $this->speedY;
						$cz = $this->speedZ;
						
						$this->speedX = $savedSpeedX;
						$this->speedY = $this->stepHeight;
						$this->speedZ = $savedSpeedZ;
						
						$aabb = clone $this->boundingBox;
						$this->boundingBox->setBB($ultraMegaOldBB);
						$aaBBs = $this->level->getCubes($this, $this->boundingBox->addCoord($this->speedX, $this->speedY, $this->speedZ));
						
						foreach($aaBBs as $bb){ //TODO optimize
							$this->speedY = $bb->calculateYOffset($this->boundingBox, $this->speedY);
						}
						$this->boundingBox->offset(0, $this->speedY, 0);
						
						foreach($aaBBs as $bb){
							$this->speedX = $bb->calculateXOffset($this->boundingBox, $this->speedX);
						}
						$this->boundingBox->offset($this->speedX, 0, 0);
						
						foreach($aaBBs as $bb){
							$this->speedZ = $bb->calculateZOffset($this->boundingBox, $this->speedZ);
						}
						$this->boundingBox->offset(0, 0, $this->speedZ);
						
						if ($cx*$cx + $cz*$cz >= $this->speedX*$this->speedX + $this->speedZ*$this->speedZ)
						{
							$this->speedX = $cx;
							$this->speedY = $cy;
							$this->speedZ = $cz;
							$this->boundingBox->setBB($aabb);
						}else{
							$stepSuccess = true;
						}
					}
					
				}
				
				if($this->enableAutojump && $this instanceof Creature){
					if(!$stepSuccess && $fallingFlag && ($this->speedX != $savedSpeedX || $this->speedZ != $savedSpeedZ)){
						$this->ai->mobController->setJumping(true);
					}else{
						$this->ai->mobController->setJumping(false);
					}
				}
				
				$support = $savedSpeedY != $this->speedY && $savedSpeedY < 0;
				if($this->speedX != 0){
					$this->x += $this->speedX;
					$update = true;
				}
				if($this->speedZ != 0){
					$this->z += $this->speedZ;
					$update = true;
				}
				if($this->speedY != 0){
					$ny = $this->y + $this->speedY;
					if($stepSuccess) $this->speedY = $beforeStepSpeedY;
					if($this->class === ENTITY_FALLING){
						if($support){
							$this->level->fastSetBlockUpdate($this->x, $this->y, $this->z, $this->data["Tile"], 0);
							$this->close();
							return;
						}
						$id = $this->level->level->getBlockID($this->x, $this->y, $this->z);
						if($id > 0 && !StaticBlock::getIsSolid($id) && !StaticBlock::getIsLiquid($id)){
							$this->server->api->entity->drop($this, BlockAPI::getItem($this->data["Tile"], 0, 1));
							$this->close();
							return;
						}
						
					}
					$this->y = $ny;
					
					$update = true;
				}
				
				$this->onGround = $support;

				
				if($this->lastX != $this->x || $this->lastZ != $this->z || $this->lastY != $this->z){
					$this->server->api->handle("entity.move", $this);
					$update = true;
				}elseif ($this->lastYaw != $this->yaw || $this->lastPitch != $this->pitch || $this->lastHeadYaw != $this->headYaw) {
					$update = true;
				}

				if($update === true){
					$hasUpdate = true;
					$this->lastSpeedZ = $this->speedZ;
					$this->lastSpeedY = $this->speedY;
					$this->lastSpeedX = $this->speedX;
				}
				$this->updateFallState($this->speedY);
			} elseif($this->player instanceof Player){
				$prevGroundState = $this->onGround;
				$this->onGround = false;
				$this->speedX = -($this->lastX - $this->x);
				$this->speedY = -($this->lastY - $this->y);
				$this->speedZ = -($this->lastZ - $this->z);
				for($x = floor($this->boundingBox->minX); $x < ceil($this->boundingBox->maxX); ++$x){
					for($z = floor($this->boundingBox->minZ); $z < ceil($this->boundingBox->maxZ); ++$z){
						for($y = floor($this->boundingBox->minY - 1); $y < ceil($this->boundingBox->maxY); ++$y){
							if($y <= floor($this->boundingBox->minY) && !$this->onGround){
								$this->onGround = StaticBlock::getIsSolid($this->level->level->getBlockID($x, $y, $z));
							}else{
								$block = $this->level->level->getBlock($x, $y, $z);
								$id = $block[0];
								$meta = $block[1];
								if($id === WATER || $id === STILL_WATER || $id === COBWEB){
									$this->fallDistance = 0;
									$this->fallStart = $this->y;
								}
							}
							
						}
					}
				}
				
				if($this->isOnLadder()){
					$this->fallDistance = 0;
					$this->fallStart = $this->y;
				}
				
				if(!$this->onGround && $prevGroundState){
					$this->fallStart = $this->y;
				}
				
				$this->updateFallState(($this->speedY <=> 0)*0.1);
				if($this->onGround) $this->fallDistance = 0;
				$hasUpdate = true;
			}
		}
		
		
		$this->counterUpdate();
		
		if($this->isPlayer() || $update){
			$this->updateMovement();
		}
		
		if($this->isPlayer()){
			$this->player->entityTick();
		}
		
		$this->needsUpdate = $hasUpdate;
		$this->lastUpdate = $now;
	}
	public function isOnLadder(){
		$x = (int)$this->x;
		$y = (int)$this->boundingBox->minY;
		$z = (int)$this->z;
		return $this->level->level->getBlockID($x, $y, $z) === LADDER;
	}
	
	public function fall(){
		if($this->isPlayer()){
			
			$x = floor($this->x);
			$y = floor($this->y - 0.2);
			$z = floor($this->z);
			$bid = $this->level->level->getBlockID($x, $y, $z);
			
			if($bid > 0){
				$clz = StaticBlock::getBlock($bid);
				$clz::fallOn($this->level, $x, $y, $z, $this, floor($this->fallStart - $this->y));
			}
			
			$dmg = floor($this->fallStart - $this->y) - 3;
			if($dmg > 0){
				$this->harm($dmg, "fall");
			}
		}
	}
	
	public function canBeShot(){
		return $this->isPlayer();
	}
	
	public function updateFallState($fallTick){
		if($this->onGround && $this->fallDistance > 0){
			$this->fall();
			$this->fallDistance = 0;
		}elseif($fallTick < 0){
			$this->fallDistance -= $fallTick;
		}
		
	}
	
	public function updateMovement()
	{
		if($this->closed === true){
			return false;
		}
		$now = microtime(true);
		if($this->isStatic === false and ($this->lastX != $this->x or $this->lastY != $this->y or $this->lastZ != $this->z or $this->lastYaw != $this->yaw or $this->lastPitch != $this->pitch or $this->lastHeadYaw != $this->headYaw)){
			if($this->class === ENTITY_PLAYER or ($this->last[5] + 8) < $now){
				if($this->server->api->handle("entity.move", $this) === false){
					if($this->class === ENTITY_PLAYER){
						$this->player->teleport(new Vector3($this->last[0], $this->last[1], $this->last[2]), $this->last[3], $this->last[4]);
					}
				} else{
					$players = $this->server->api->player->getAll($this->level);
					if($this->player instanceof Player){
						$this->updateLast();
						unset($players[$this->player->CID]);
						$pk = new MovePlayerPacket();
						$pk->eid = $this->eid;
						$pk->x = $this->x;
						$pk->y = $this->y;
						$pk->z = $this->z;
						$pk->yaw = $this->yaw;
						$pk->pitch = $this->pitch;
						$pk->bodyYaw = $this->yaw;
						$this->server->api->player->broadcastPacket($players, $pk);
					}
				}
			} else{
				$this->updatePosition();
			}
			
		}
		$this->lastUpdate = $now;
	}
	
	/**
	 * Handle fall out of world
	 */
	public function outOfWorld(){
		if($this->isPlayer()){
			$this->health = 0;
			$this->makeDead("void");
		}else{
			$this->close();
		}
	}
	
	public function getEyeHeight(){ //TODO in vanilla player's eyeHeight is 0.12
		return $this->isPlayer() ? $this->height - 0.12 : $this->width * 0.85;
	}
	
	public function interactWith(Entity $e, $action)
	{
		if($this->class === ENTITY_PLAYER and ($this->server->api->getProperty("pvp") == false or $this->server->difficulty <= 0 or ($e->player->gamemode & 0x01) === 0x01)){
			return false;
		}

		if($action === InteractPacket::ACTION_ATTACK && $e->isPlayer()){
			$slot = $e->player->getHeldItem();
			$damage = $slot->getDamageAgainstOf($e);
			$this->harm($damage, $e->eid);
			if($slot->isTool() === true and ($e->player->gamemode & 0x01) === 0){
				if($slot->useOn($e) and $slot->getMetadata() >= $slot->getMaxDurability()){
					$e->player->removeItem($slot->getID(), $slot->getMetadata(), 1, true);
				}
			}
			return true;
		}
		return false;
	}

	public function getDirection()
	{
		$rotation = ($this->yaw - 90) % 360;
		if($rotation < 0){
			$rotation += 360.0;
		}
		if((0 <= $rotation and $rotation < 45) or (315 <= $rotation and $rotation < 360)){
			return 2; //x-
		} elseif(45 <= $rotation and $rotation < 135){
			return 3; //z-
		} elseif(135 <= $rotation and $rotation < 225){
			return 0; //x+
		} elseif(225 <= $rotation and $rotation < 315){
			return 1; //z+
		} else{
			return null;
		}
	}

	/**
	 * METADATA VALUES
	 * *****************
	 * Types: Get input type of <value>
	 * 0 -> Byte
	 * 1 -> Short
	 * 2 -> Integer
	 * 3 -> Float
	 * 4 -> Length of <value>, Short
	 * 5 -> [Short, Byte, Short]
	 * 6 -> [Integer, Integer, Integer]
	 * *****************
	 * 0 => ["type" => 0, "value" => $flags] --> DATA_FLAGS
	 * 1 => ["type" => 1, "value" => $this->air] --> Entity Air
	 * 14 => ["type" => 0, "value" => 1] --> IsBaby, value: 0 => false, 1 => true
	 * 16 => ["type" => 0, "value" => 0] --> Fuse(TNT), Saddled(Pig), Creeper(29 ticks before explosion)
	 * 17 => ["type" => 6, "value" => [0, 0, 0]] --> Bed Position <?>
	 *
	 *
	 * DATA FLAGS IDS
	 * 0 - fire
	 * 1 - crouching
	 * 4 - inAction(ex.: using a bow)
	 */
	public function getMetadata()
	{
		$flags = 0;
		$flags ^= $this->fire > 0 ? 0b1 : 0;
		$flags ^= ($this->crouched ? 0b1 : 0) << 1;
		$flags ^= ($this->inAction ? 0b1 : 0) << 4;
		$d = [
			0 => [
				"type" => 0,
				"value" => $flags
			],
			1 => [
				"type" => 1,
				"value" => $this->air
			],
			14 => [
				"type" => 0,
				"value" => 0
			],
			16 => [
				"type" => 0,
				"value" => 0
			],
			17 => [
				"type" => 6,
				"value" => [
					0,
					0,
					0
				]
			]
		];
		$d[16]["value"] = $this->data["State"];
		if($this->isPlayer()){
			if($this->player->isSleeping !== false){
				$d[16]["value"] = 2;
				$d[17]["value"] = [
					$this->player->isSleeping->x,
					$this->player->isSleeping->y,
					$this->player->isSleeping->z
				];
			}
		}
		return $d;
	}

	public function updateMetadata()
	{
		$this->server->api->dhandle("entity.metadata", $this);
	}

	public function spawn($player)
	{
		if(! ($player instanceof Player)){
			$player = $this->server->api->player->get($player);
		}
		if($player->eid === $this->eid or $this->closed !== false or ($player->level !== $this->level and $this->class !== ENTITY_PLAYER)){
			return false;
		}
		switch($this->class) {
			case ENTITY_PLAYER:
				if($this->player->connected !== true or $this->player->spawned === false){
					return false;
				}
				if($this->player->gamemode === SPECTATOR){
					break;
				}
				$pk = new AddPlayerPacket();
				$pk->clientID = 0; // $this->player->clientID;
				$pk->username = $this->player->username;
				$pk->eid = $this->eid;
				$pk->x = $this->x;
				$pk->y = $this->y;
				$pk->z = $this->z;
				$pk->yaw = 0;
				$pk->pitch = 0;
				$pk->itemID = 0;
				$pk->itemAuxValue = 0;
				$pk->metadata = $this->getMetadata();
				$player->dataPacket($pk);

				$pk = new SetEntityMotionPacket();
				$pk->eid = $this->eid;
				$pk->speedX = $this->speedX;
				$pk->speedY = $this->speedY;
				$pk->speedZ = $this->speedZ;
				$player->dataPacket($pk);

				$pk = new PlayerEquipmentPacket();
				$pk->eid = $this->eid;
				$pk->item = $this->player->getSlot($this->player->slot)->getID();
				$pk->meta = $this->player->getSlot($this->player->slot)->getMetadata();
				$pk->slot = 0;
				$player->dataPacket($pk);
				$this->player->sendArmor($player);
				break;
			case ENTITY_ITEM:
				$pk = new AddItemEntityPacket();
				$pk->eid = $this->eid;
				$pk->x = $this->x;
				$pk->y = $this->y;
				$pk->z = $this->z;
				$pk->yaw = $this->yaw;
				$pk->pitch = $this->pitch;
				$pk->roll = 0;
				$pk->item = BlockAPI::getItem($this->type, $this->meta, $this->stack);
				$pk->metadata = $this->getMetadata();
				$player->dataPacket($pk);

				$pk = new SetEntityMotionPacket();
				$pk->eid = $this->eid;
				$pk->speedX = $this->speedX;
				$pk->speedY = $this->speedY;
				$pk->speedZ = $this->speedZ;
				$player->dataPacket($pk);
				break;
			case ENTITY_FALLING:
				$pk = new AddEntityPacket();
				$pk->eid = $this->eid;
				$pk->type = $this->type;
				$pk->x = $this->x;
				$pk->y = $this->y;
				$pk->z = $this->z;
				$pk->did = - $this->data["Tile"];
				$player->dataPacket($pk);

				$pk = new SetEntityMotionPacket();
				$pk->eid = $this->eid;
				$pk->speedX = $this->speedX;
				$pk->speedY = $this->speedY;
				$pk->speedZ = $this->speedZ;
				$player->dataPacket($pk);
				break;
		}
	}
	
	public function counterUpdate(){
		if($this->knockbackTime > 0){
			--$this->knockbackTime;
		}
		if($this->delayBeforePickup > 0){
			--$this->delayBeforePickup;
		}
		if($this->moveTime > 0){
			--$this->moveTime;
		}
		if($this->lookTime > 0){
			--$this->lookTime;
		}
		if($this->idleTime > 0){
			--$this->idleTime;
		}
		
		
		if($this->inAction){
			++$this->inActionCounter;
		}
	}
	
	public function close()
	{
		if($this->closed === false){
			$this->closed = true;
			$this->server->api->entity->remove($this->eid);
		}
	}

	public function __destruct()
	{
		$this->close();
	}

	public function getEID()
	{
		return $this->eid;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		// $this->server->query("UPDATE entities SET name = '".str_replace("'", "", $this->name)."' WHERE EID = ".$this->eid.";"); is this neccessary for database?
	}

	public function look($pos2)
	{
		$pos = $this->getPosition();
		$angle = Utils::angle3D($pos2, $pos);
		$this->yaw = $angle["yaw"];
		$this->pitch = $angle["pitch"];
		$this->server->query("UPDATE entities SET pitch = " . $this->pitch . ", yaw = " . $this->yaw . " WHERE EID = " . $this->eid . ";");
	}

	public function move(Vector3 $pos, $yaw = 0, $pitch = 0)
	{
		$this->x += $pos->x;
		$this->y += $pos->y;
		$this->z += $pos->z;
		$this->yaw += $yaw;
		$this->yaw %= 360;
		$this->pitch += $pitch;
		$this->pitch %= 90;
		$this->server->query("UPDATE entities SET x = " . $this->x . ", y = " . $this->y . ", z = " . $this->z . ", pitch = " . $this->pitch . ", yaw = " . $this->yaw . " WHERE EID = " . $this->eid . ";");
		$this->updateAABB();
	}

	public function updateAABB()
	{
		$this->boundingBox->setBounds(
			$this->x - $this->radius, $this->y, $this->z - $this->radius, 
			$this->x + $this->radius, $this->y + $this->height, $this->z + $this->radius
		);
	}

	public function updatePosition()
	{
		$this->server->query("UPDATE entities SET level = '" . $this->level->getName() . "', x = " . $this->x . ", y = " . $this->y . ", z = " . $this->z . ", pitch = " . $this->pitch . ", yaw = " . $this->yaw . " WHERE EID = " . $this->eid . ";");
		$this->sendMoveUpdate();
		$this->updateAABB();
	}
	
	public function setPosition(Vector3 $pos, $yaw = false, $pitch = false)
	{
		if($pos instanceof Position and $pos->level instanceof Level and $this->level !== $pos->level){
			$this->level = $pos->level;
			$this->server->preparedSQL->entity->setLevel->reset();
			$this->server->preparedSQL->entity->setLevel->clear();
			$this->server->preparedSQL->entity->setLevel->bindValue(":level", $this->level->getName(), SQLITE3_TEXT);
			$this->server->preparedSQL->entity->setLevel->bindValue(":eid", $this->eid, SQLITE3_INTEGER);
			$this->server->preparedSQL->entity->setLevel->execute();
		}
		$this->x = $pos->x;
		$this->y = $pos->y;
		$this->z = $pos->z;
		if($yaw !== false){
			$this->yaw = $yaw;
		}
		if($pitch !== false){
			$this->pitch = $pitch;
		}
		$this->server->preparedSQL->entity->setPosition->reset();
		$this->server->preparedSQL->entity->setPosition->clear();
		$this->server->preparedSQL->entity->setPosition->bindValue(":x", $this->x, SQLITE3_TEXT);
		$this->server->preparedSQL->entity->setPosition->bindValue(":y", $this->y, SQLITE3_TEXT);
		$this->server->preparedSQL->entity->setPosition->bindValue(":z", $this->z, SQLITE3_TEXT);
		$this->server->preparedSQL->entity->setPosition->bindValue(":pitch", $this->pitch, SQLITE3_TEXT);
		$this->server->preparedSQL->entity->setPosition->bindValue(":yaw", $this->yaw, SQLITE3_TEXT);
		$this->server->preparedSQL->entity->setPosition->bindValue(":eid", $this->eid, SQLITE3_INTEGER);
		$this->server->preparedSQL->entity->setPosition->execute();
	}
	public function inBlockNonVector($x, $y, $z, $radius = 0.8)
	{
		return $y == ceil($this->y) || $y == (ceil($this->y)+1) && max(abs($x - ($this->x-0.5)), abs($z - ($this->z-0.5)));
	}
	public function inBlock(Vector3 $block, $radius = 0.8)
	{
		$me = new Vector3($this->x - 0.5, $this->y, $this->z - 0.5);
		if(($block->y == (ceil($this->y)) or $block->y == (ceil($this->y) + 1)) and $block->maxPlainDistance($me) < $radius){
			return true;
		}
		return false;
	}

	public function touchingBlock(Vector3 $block, $radius = 0.9)
	{
		$me = new Vector3($this->x - 0.5, $this->y, $this->z - 0.5);
		if(($block->y == (((int) $this->y) - 1) or $block->y == ((int) $this->y) or $block->y == (((int) $this->y) + 1)) and $block->maxPlainDistance($me) < $radius){
			return true;
		}
		return false;
	}

	public function isSupport(Vector3 $pos, $radius = 1)
	{
		$me = new Vector2($this->x - 0.5, $this->z - 0.5);
		$diff = $this->y - $pos->y;
		if($me->distance(new Vector2($pos->x, $pos->z)) < $radius and $diff > - 0.7 and $diff < 1.6){
			return true;
		}
		return false;
	}

	public function resetSpeed()
	{
		$this->speedMeasure = array(0, 0, 0, 0, 0, 0, 0);
	}

	public function getSpeed()
	{
		return $this->speed;
	}

	public function getSpeedMeasure()
	{
		return array_sum($this->speedMeasure) / count($this->speedMeasure);
	}

	public function calculateVelocity()
	{
		$diffTime = max(0.05, abs(microtime(true) - $this->last[5]));
		$origin = new Vector2($this->last[0], $this->last[2]);
		$final = new Vector2($this->x, $this->z);
		$speedX = ($this->last[0] - $this->x) / $diffTime;
		$speedY = ($this->last[1] - $this->y) / $diffTime;
		$speedZ = ($this->last[2] - $this->z) / $diffTime;
		if($this->speedX != $speedX or $this->speedY != $speedY or $this->speedZ != $speedZ){
			$this->speedX = $speedX;
			$this->speedY = $speedY;
			$this->speedZ = $speedZ;
			$this->server->api->handle("entity.motion", $this);
		}
		$this->speed = $origin->distance($final) / $diffTime;
		unset($this->speedMeasure[key($this->speedMeasure)]);
		$this->speedMeasure[] = $this->speed;
	}
	/**
	 * @return array
	 */
	public function createSaveData(){
		$data = [
			"id" => $this->type,
			"Health" => $this->health,
			"Pos" => [
				0 => $this->x,
				1 => $this->y,
				2 => $this->z,
			],
			"Rotation" => [
				0 => $this->yaw,
				1 => $this->pitch,
			],
			
		];
		if($this->class === ENTITY_OBJECT){
			$data["TileX"] = $this->x;
			$data["TileY"] = $this->y;
			$data["TileZ"] = $this->z;
		}
		if($this->class === ENTITY_FALLING){
			$data["Tile"] = $this->data["Tile"];
		}
		if($this->class === ENTITY_ITEM){
			$data["Item"] = [
				"id" => $this->type,
				"Damage" => $this->meta,
				"Count" => $this->stack,
			];
		}
		return $data;
	}
 
	public function updateLast()
	{
		$this->last[0] = $this->x;
		$this->last[1] = $this->y;
		$this->last[2] = $this->z;
		$this->last[3] = $this->yaw;
		$this->last[4] = $this->pitch;
		$this->last[5] = microtime(true);
	}

	public function getPosition($round = false)
	{
		return ! isset($this->position) ? false : ($round === true ? array_map("floor", $this->position) : $this->position);
	}

	public function harm($dmg, $cause = "generic", $force = false)
	{
		if (! $this->canBeAttacked) {
			return false;
		}
		$dmg = $this->applyArmor($dmg, $cause); //TODO HURTCAM
		$ret = $this->setHealth(max(- 128, $this->getHealth() - ((int) $dmg)), $cause, $force);

		if ($ret != false && $this->hasKnockback && is_numeric($cause) && ($entity = $this->server->api->entity->get($cause)) != false) {
			$d = $entity->x - $this->x;

			for ($d1 = $entity->z - $this->z; $d * $d + $d1 * $d1 < 0.0001; $d1 = (lcg_value() - lcg_value()) * 0.01) {
				$d = (lcg_value() - lcg_value()) * 0.01;
			}
			
			$this->knockBack($d, $d1);
			$this->knockbackTime = 10;
			
		}

		return $ret;
	}

	public function setState($v)
	{
		$this->state = $v;
		$this->data["State"] = $v;
		$this->updateMetadata();
	}

	public function getState()
	{
		return $this->state;
	}

	public function heal($health, $cause = "generic")
	{
		return $this->setHealth(min(20, $this->getHealth() + ((int) $health)), $cause);
	}

	public function linkEntity(Entity $e, $type)
	{
		$this->linkedEntity = $e;
		$e->linkedEntity = $this;
		$this->server->api->dhandle("entity.link", ["rider" => $e->eid, "riding" => $this->eid, "type" => 0]);
	}

	public function isPlayer()
	{
		return isset($this->player) && $this->player instanceof Player;
	}

	public function sendMoveUpdate()
	{
		if($this->class === ENTITY_PLAYER){
			$this->player->teleport(new Vector3($this->x, $this->y, $this->z));
			return;
		}
	}

	public function moveEntityWithOffset($oX, $oY, $oZ)
	{
		$oX = $oX === 0 ? $this->speedX : ($this->getSpeedModifer() * $oX * $this->getSpeed());
		$oY = $oY <= 0 ? $this->speedY : (0.40);
		$oZ = $oZ === 0 ? $this->speedZ : ($this->getSpeedModifer() * $oZ * $this->getSpeed());
		$this->setVelocity($oX, $oY, $oZ);
	}

	public function getSpeedModifer()
	{
		return $this->speedModifer;
	}
	public function getArmorValue(){
		$pnts = 0;
		if($this->isPlayer()){
			foreach($this->player->armor as $slot => $part){
				if($part instanceof ArmorItem){
					$pnts += $part->getDamageReduceAmount();
					$this->player->damageArmorPart($slot, $part);
				}
			}
			$this->player->sendArmor($this->player);
		}
		return $pnts;
	}
	public function applyArmor($damage, $cause){
		if(is_numeric($cause) || $cause === "explosion"){
			$var3 = 25 - $this->getArmorValue();
			$var4 = $damage * $var3 + $this->carryoverDamage;
			$damage = $var4 / 25;
			$this->carryoverDamage = $var4 % 25;
		}
		return $damage;
	}
	
	public function setHealth($health, $cause = "generic", $force = false)
	{
		$health = (int) $health;
		$harm = false;
		if($health < $this->health){
			$harm = true;
			$dmg = $this->health - $health;
			if(($this->class !== ENTITY_PLAYER or (($this->player instanceof Player) and (($this->player->gamemode & 0x01) === 0x00 or $force === true))) and ($this->dmgcounter[0] < microtime(true) or $this->dmgcounter[1] < $dmg) and ! $this->dead){
				$this->dmgcounter[0] = microtime(true) + 0.5;
				$this->dmgcounter[1] = $dmg;
			} else{
				return false; // Entity inmunity
			}
		} elseif($health === $this->health and ! $this->dead){
			return false;
		}
		if($this->server->api->dhandle("entity.health.change", array(
			"entity" => $this,
			"eid" => $this->eid,
			"health" => $health,
			"cause" => $cause
		)) !== false or $force === true){
			$this->health = min(127, max(- 127, $health));
			$this->server->query("UPDATE entities SET health = " . $this->health . " WHERE EID = " . $this->eid . ";");
			if($harm === true){
				$pk = new EntityEventPacket;
				$pk->eid = $this->eid;
				$pk->event = EntityEventPacket::ENTITY_DAMAGE;
				$this->server->api->player->broadcastPacket($this->level->players, $pk);
			}
			if($this->player instanceof Player){
				$pk = new SetHealthPacket();
				$pk->health = $this->health;
				$this->player->dataPacket($pk);
			}
			if($this->health <= 0 and $this->dead === false){
				$this->makeDead($cause);
			} elseif($this->health > 0){
				$this->dead = false;
			}
			return true;
		}
		return false;
	}

	public function setSize($w, $h)
	{
		$this->width = $w;
		$this->height = $h;
		$this->radius = $w / 2;
	}
	
	public function makeDead($cause){
		if($this->server->api->dhandle("entity.death", ["entity" => $this, "cause" => $cause]) === false) return false;
		$this->spawnDrops();
		$this->air = $this->maxAir;
		$this->fire = 0;
		$this->crouched = false;
		$this->fallY = false;
		$this->fallStart = false;
		$this->updateMetadata();
		$this->dead = true;
		if($this->player instanceof Player){
			$pk = new MoveEntityPacket_PosRot();
			$pk->eid = $this->eid;
			$pk->x = -256;
			$pk->y = 128;
			$pk->z = -256;
			$pk->yaw = 0;
			$pk->pitch = 0;
			$this->server->api->player->broadcastPacket($this->level->players, $pk);
		}else{
			$pk = new EntityEventPacket;
			$pk->eid = $this->eid;
			$pk->event = EntityEventPacket::ENTITY_DEAD;
			$this->server->api->player->broadcastPacket($this->level->players, $pk);
		}

		if($this->player instanceof Player){
			$this->player->blocked = true;
			$this->server->api->dhandle("player.death", [
				"player" => $this->player,
				"cause" => $cause
			]);
			if($this->server->api->getProperty("hardcore") == 1){ //poor player =<
				$this->server->api->ban->ban($this->player->username);
			}
		} else{
			if($this instanceof Painting || $this instanceof Minecart){ //TODO better fix
				$this->close();
			}
			else{
				$this->server->api->schedule(40, [$this, "close"], []);
			}
		}
	}
	
	public function getAttackDamage(){
		return 0;
	}
	
	public function setSpeed($s)
	{
		$this->speed = $s;
	}

	public function knockBack($d, $d1)
	{
		if($this->closed || $this->dead){
			return false;
		}
		$f = sqrt($d * $d + $d1 * $d1);
		$f1 = 0.4;
		$this->speedX /= 2;
		$this->speedZ /= 2;
		$this->speedX -= ($d / $f) * $f1;
		$this->speedY += 0.4;
		$this->speedZ -= ($d1 / $f) * $f1;
		if($this->speedY > 0.4){
			$this->speedY = 0.4;
		}
	}

	public function getHealth()
	{
		return $this->health;
	}

	public function __toString()
	{
		return "Entity(x={$this->x},y={$this->y},z={$this->z},level=" . $this->level->getName() . ",class={$this->class},type={$this->type})";
	}
	
	/**
	 * Debug
	 */
	public function printSpeed($add = ""){
		ConsoleAPI::debug("$add {$this->speedX}:{$this->speedY}:{$this->speedZ}");
	}
	
	/*
	 * Deprecated methods.
	 * Those methods were left only for compability with older plugins
	 */
	/**
	 *
	 * @deprecated Use {@link getHeightOf} or {@link getWidthOf} instead
	 * @throws Exception
	 */
	public static function getSizeOf($e)
	{
		throw new Exception("Use getHeightOf or getWidthOf method instead of this.");
	}
	
	
	/**
	 *
	 * @deprecated Use {@link getHeight} or {@link getWidth} instead
	 * @throws Exception
	 */
	public function getSize()
	{
		throw new Exception("Use getHeight or getWidth method instead of this.");
	}
}
