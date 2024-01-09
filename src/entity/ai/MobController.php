<?php

class MobController
{
	public static $ADVANCED = false;
	public static $AUTOJUMP = false;
	public static $landed = false;
	public static $DANGEROUS_BLOCKS = [
		LAVA => true,
		STILL_LAVA => true,
		FIRE => true,
	];
	/**
	 * @var Living
	 */
	public $entity;
	
	public $finalYaw, $finalPitch, $finalHeadYaw;
	
	public $headYaw;
	public $someTicker = 0;
	
	protected $jumping;
	protected $jumpTimeout;
	
	public $moveToX, $moveToY, $moveToZ;
	public $speed;
	public $updateMove = false;
	
	public function __construct($e){
		$this->entity = $e;
	}
	public function isDangerous($id){
		return isset(self::$DANGEROUS_BLOCKS[$id]);
	}
	public function isJumping(){
		return $this->jumping;
	}
	
	public function setJumping($b){
		$this->jumping = $b;
	}
	
	public function isRotationCompleted(){
		return $this->finalHeadYaw === $this->entity->headYaw;
	}
	
	public function setMovingOffset($x, $y, $z, $speed){
		$this->moveToX = $this->entity->x + ($x);
		$this->moveToY = $this->entity->y + ($y);
		$this->moveToZ = $this->entity->z + ($z);
		$this->speed = $speed;
		$this->updateMove = true;
	}
	
	public function canJump(){
		return $this->isJumping() && $this->jumpTimeout <= 0 && $this->entity->onGround;
	}
	public static function limitAngle2($old, $newA, $limit){
		$v4 = Utils::wrapAngleTo180($old - $newA);
		
		if($v4 < -$limit) $v4 = -$limit;
		if($v4 >= $limit) $v4 = $limit;
		
		return $old - $v4;
	}
	public static function limitAngle($old, $newA, $limit){
		$v4 = Utils::wrapAngleTo180($newA - $old);
		
		if($v4 > $limit) $v4 = $limit;
		if($v4 < -$limit) $v4 = -$limit;
		
		return $old + $v4;
	}
	
	public function movementTick(){
		$this->entity->moveForward = 0;
		if($this->updateMove){
			$this->updateMove = false;
			$v1 = floor($this->entity->boundingBox->minY + 0.5);
			
			$diffX = $this->moveToX - $this->entity->x;
			$diffZ = $this->moveToZ - $this->entity->z;
			$diffY = $this->moveToY - $v1;
			
			$v8 = $diffX*$diffX + $diffY*$diffY + $diffZ*$diffZ;
			
			if($v8 >= 2.500000277905201E-7){ //TODO convert notation
				$v10 = (atan2($diffZ, $diffX) * 180 / M_PI) - 90;
				$this->entity->yaw = self::limitAngle($this->entity->yaw, $v10, 30);
				$this->entity->setAIMoveSpeed($this->speed * $this->entity->getSpeedModifer());
				
				if($diffY > 0 && $diffX*$diffX + $diffZ*$diffZ < 1) $this->setJumping(true);
			}
		}
		//TODO handle jumps somewhere
		//if($this->canJump()){
		//	$this->jumpTimeout = 10;
		//	$this->entity->speedY = 0.50;
		//}
		//if($this->jumpTimeout > 0) --$this->jumpTimeout;
	}
	
	public function updateHeadYaw(){
		$diffX = $this->entity->x - $this->entity->lastX;
		$diffZ = $this->entity->z - $this->entity->lastZ;
		if(($diffX*$diffX + $diffZ*$diffZ) > 2.500000277905201E-7){ //TODO convert notation
			$this->entity->renderYawOffset = $this->entity->yaw;
			$this->entity->headYaw = self::limitAngle2($this->entity->renderYawOffset, $this->entity->headYaw, 75);
			$this->headYaw = $this->entity->headYaw;
			$this->someTicker = 0;
		}else{
			$v5 = 75;
			if(abs($this->entity->headYaw - $this->headYaw) > 15){
				$this->someTicker = 0;
				$this->headYaw = $this->entity->headYaw;
			}else{
				++$this->someTicker;
				
				if($this->someTicker > 10){
					$v5 = max(1 - ($this->someTicker - 10) / 10, 0) * 75;
				}
			}
			$this->entity->renderYawOffset = self::limitAngle2($this->entity->headYaw, $this->entity->renderYawOffset, $v5);
		}
		$this->entity->headYaw = $this->entity->renderYawOffset;
	}
	
	public function rotateTick(){ //TODO handle more rotation
		
		
		
		//$this->entity->lastHeadYaw = $this->entity->headYaw;
		//$w180 = Utils::wrapAngleTo180($this->finalHeadYaw - $this->entity->headYaw); 
		//$w180min = min(abs($w180), 20)*Utils::getSign($w180);
		//$this->entity->headYaw = Utils::wrapAngleTo360($this->entity->headYaw + $w180min);
	}
	
	public function moveTo($x, $y, $z, $camera = true){
		//TODO fix return $this->moveNonInstant($x - floor($this->entity->x), $y - floor($this->entity->y), $z - floor($this->entity->z), $camera);
	}
	
	public function faceEntity($x, $y, $z){
		$len = sqrt($x*$x + $z*$z + $y*$y);
		//$d = $len == 0 ?//$v->subtract($this->entity)->normalize();
		if($len == 0){
			$dx = 0;
			$dz = 0;
		}else{
			$dx = $x / $len;
			$dz = $z / $len;
		}
		
		
		$tan = $dz == 0 ? ($dx < 0 ? 180 : 0) : (90 - rad2deg(atan($dx / $dz))); 
		$thetaOffset = $dz < 0 ? 90 : 270;
		$calcYaw = ($thetaOffset + $tan);
		$this->finalHeadYaw = $this->entity->yaw = Utils::wrapAngleTo360($calcYaw);
	}
	
	public function lookOffset($x, $y, $z, $pitch = true){
		$tan = $z == 0 ? ($x < 0 ? 180 : 0) : (90 - rad2deg(atan($x / $z))); /*arctan(infinity) = pi/2 = (90deg) - 90 = 0*/
		$thetaOffset = $z < 0 ? 90 : 270;
		$calcYaw = $tan + $thetaOffset;
		
		$this->entity->yaw = $this->finalHeadYaw = Utils::wrapAngleTo360($calcYaw);
		
		if($pitch){
			$diff = sqrt($x * $x + $z * $z);
			$calcPitch = $diff == 0 ? ($y < 0 ? -90 : 90) : rad2deg(atan($y / $diff));
			$this->entity->pitch = $calcPitch;
		}
		
		//$this->entity->server->query("UPDATE entities SET pitch = ".$this->entity->pitch.", yaw = ".$this->entity->yaw." WHERE EID = ".$this->entity->eid.";");
		return true;
	}
	
	public function lookOn($x, $y = 0, $z = 0, $pitch = true){
		if($x instanceof Vector3){
			return $this->lookOn($x->x, $x->y + $x->getEyeHeight(), $x->z, $pitch);
		}
		return $this->lookOffset($x - $this->entity->x, ($this->entity->y + $this->entity->height) - $y, $z - $this->entity->z, $pitch);
	}
	
	public function __destruct(){
		unset($this->entity);
	}
}

