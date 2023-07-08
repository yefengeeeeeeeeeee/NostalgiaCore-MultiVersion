<?php

class Arrow extends Projectile{
	const TYPE = OBJECT_ARROW;
	
	public $criticial = false;
	public $shooterEID = 0;
	public $shotByEntity;
	public $airTicks = 0;
	public $inWall = false;
	public $groundTicks = 0;
	function __construct(Level $level, $eid, $class, $type = 0, $data = [], $shooter = false){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->gravity = 0.05;
		$this->setSize(0.5, 0.5);
		$this->setName("Arrow");
		$this->shooterEID = $shooter;
		$this->shotByEntity = $shooter instanceof Entity;
		$this->airTicks = $this->groundTicks = 0;
		//$this->server->schedule(1210, array($this, "update")); //Despawn
	}
	
	public function handleUpdate(){
		$pk = new MoveEntityPacket_PosRot;
		$pk->eid = $this->eid;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$this->server->api->player->broadcastPacket($this->level->players, $pk);
	}
	
	public function shoot($d, $d1, $d2, $f, $f1){ //original name from 0.8.1 IDA decompilation, var names are taken from b1.7.3
		$f2 = sqrt($d * $d + $d1 * $d1 + $d2 * $d2);
		$d /= $f2;
		$d1 /= $f2;
		$d2 /= $f2;
		$d += $this->random->nextGaussian() * 0.0075 * $f1; //0.0074999998323619366 replaced with 0.0075
		$d1 += $this->random->nextGaussian() * 0.0075 * $f1;
		$d2 += $this->random->nextGaussian() * 0.0075 * $f1;
		$d *= $f;
		$d1 *= $f;
		$d2 *= $f;
		$this->speedX = $d;
		$this->speedY = $d1;
		$this->speedZ = $d2;
		$f3 = sqrt($d * $d + $d2 * $d2);
		$this->yaw = (atan2($d, $d2) * 180) / M_PI;
		$this->pitch = (atan2($d1, $f3) * 180) / M_PI;
		$this->sendMotion();
		$this->updatePosition();
		$this->update();
		//TODO i guess? $ticksInGround = 0;
	}
	public function sendMoveUpdate()
	{
		$pk = new MoveEntityPacket_PosRot();
		$pk->eid = $this->eid;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		foreach($this->level->players as $p){ //sending packets directly makes movement less laggy
			$p->directDataPacket(clone $pk);
		}
	}
	public function update(){
		$this->needsUpdate = false; //TODO reenable
		return;
		//parent::update();
		if($this->closed || ($this->x > 255 || $this->x < 0 || $this->y < 0 || $this->z < 0 || $this->z > 255) || $this->groundTicks > 200) { //remove after 10 seconds in wall, idc about vanilla
			$this->server->api->entity->remove($this->eid);
			return;
		}
		
		$this->needsUpdate = true;
		if($this->inWall) {
			++$this->groundTicks;
			return; //yeah whatever
		}
		if($this->speedX != 0 or $this->speedY != 0 or $this->speedZ != 0){
			$f = sqrt(($this->speedX * $this->speedX) + ($this->speedZ * $this->speedZ));
			$this->yaw = (atan2($this->speedX, $this->speedZ) * 180 / M_PI);
			$this->pitch = (atan2($this->speedY, $f) * 180 / M_PI);
		}
		$rt = $this->boundingBox->shrink(0.4, 0.4, 0.4)->addCoord($this->speedX, $this->speedY, $this->speedZ);
		for($x = floor($rt->minX); $x < ceil($rt->maxX); ++$x){
			for($z = floor($rt->minZ); $z < ceil($rt->maxZ); ++$z){
				for($y = ceil($rt->minY); $y < ceil($rt->maxY); ++$y){
					$b = $this->level->level->getBlockID($x, $y, $z);
					if(StaticBlock::getIsSolid($b)){
						$bb = StaticBlock::getBoundingBoxForBlockCoords($b, $x, $y, $z);
						$this->speedY = $bb->calculateYOffset($this->boundingBox, $this->speedY);
						$this->speedX = $bb->calculateXOffset($this->boundingBox, $this->speedX);
						$this->speedZ = $bb->calculateZOffset($this->boundingBox, $this->speedZ);
						$this->inWall = true;
						break;
					}
				}
			}
		}
		if(!$this->inWall){
			$bbexp = $this->boundingBox->addCoord($this->speedX, $this->speedY, $this->speedZ)->expand(0, 0.2, 0);
			foreach($this->level->entityList as $e){
				if($e instanceof Entity && $e->eid !== $this->eid && !$e->closed && $e->canBeShot() && $e->boundingBox->intersectsWith($bbexp)){
					if($this->shotByEntity && $this->shooterEID === $e->eid && $this->airTicks < 5) continue;
					$dmg = ceil(sqrt($this->speedX * $this->speedX + $this->speedY * $this->speedY + $this->speedZ * $this->speedZ) * 2);
					if($this->criticial){
						$dmg += mt_rand(0, (int)($dmg/2+1));
					}
					$e->harm($dmg, $this->eid);
					$this->closed = true;
					break;
				}
			}
		}
		
		$this->x += $this->speedX;
		$this->y += $this->speedY;
		$this->z += $this->speedZ;
		++$this->airTicks; //TODO onGround state
		
		$this->speedX *= 0.99;
		$this->speedY *= 0.99;
		$this->speedZ *= 0.99;
		$this->speedY -= $this->gravity;
		
		
		$this->sendMotion();
		$this->updatePosition();	
		
	}
	
	public function spawn($player){
		if($this->type === OBJECT_ARROW){
			$pk = new AddEntityPacket;
			$pk->eid = $this->eid;
			$pk->type = $this->type;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->did = 1;		
			$pk->speedX = $this->speedX;
			$pk->speedY = $this->speedY;
			$pk->speedZ = $this->speedZ;
			$player->dataPacket($pk);
			
			$pk = new SetEntityMotionPacket;
			$pk->eid = $this->eid;
			$pk->speedX = $this->speedX;
			$pk->speedY = $this->speedY;
			$pk->speedZ = $this->speedZ;
			$player->dataPacket($pk);
		}
	}
}