<?php

class Arrow extends Projectile{
	const TYPE = OBJECT_ARROW;
	function __construct(Level $level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->gravity = 0.05;
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
		//parent::update();
		$this->needsUpdate = true;
		
		if($this->speedX != 0 or $this->speedY != 0 or $this->speedZ != 0){
			$f = sqrt(($this->speedX * $this->speedX) + ($this->speedZ * $this->speedZ));
			$this->yaw = (atan2($this->speedX, $this->speedZ) * 180 / M_PI);
			$this->pitch = (atan2($this->speedY, $f) * 180 / M_PI);
		}
		$this->x += $this->speedX;
		$this->y += $this->speedY;
		$this->z += $this->speedZ;
		
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