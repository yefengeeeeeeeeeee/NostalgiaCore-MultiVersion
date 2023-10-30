<?php

class MobController
{
	public static $ADVANCED = false;
	public static $landed = false;
	public static $DANGEROUS_BLOCKS = [
		LAVA => true,
		STILL_LAVA => true,
		FIRE => true,
	];
	/**
	 * @var Entity
	 */
	public $entity;
	
	public $finalYaw, $finalPitch, $finalHeadYaw;
	
	protected $jumping;
	protected $jumpTimeout;
	
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
	
	public function moveNonInstant($x, $y, $z, $camera = true){
		if($x == 0 && $y == 0 && $z == 0){
			return false;
		}
		
		$ox = ($x > 0 ? 1 : ($x < 0 ? -1 : 0));
		$oy = ($y > 0 ? 1 : ($y < 0 ? -1 : 0));
		$oz = ($z > 0 ? 1 : ($z < 0 ? -1 : 0));
		$xf = $this->entity->x + ($this->entity->getSpeedModifer() * $ox * $this->entity->getSpeed());
		$zf = $this->entity->z + ($this->entity->getSpeedModifer() * $oz * $this->entity->getSpeed());
		$a = $b = $c = $d = 0;
		$oy = $this->entity->onGround && (
				StaticBlock::getIsSolid(($a = $this->entity->level->level->getBlockID(ceil($xf), (int)($this->entity->y), ceil($zf)))) && 
				!StaticBlock::getIsSolid($this->entity->level->level->getBlockID(ceil($xf), (int)($this->entity->y) + 1, ceil($zf)))
			||
				StaticBlock::getIsSolid(($b = $this->entity->level->level->getBlockID(ceil($xf), (int)($this->entity->y), $zf - ($oz < 0)))) &&
				!StaticBlock::getIsSolid($this->entity->level->level->getBlockID(ceil($xf), (int)($this->entity->y) + 1, $zf - ($oz < 0)))
			||
				StaticBlock::getIsSolid(($c = $this->entity->level->level->getBlockID($xf - ($ox < 0), (int)($this->entity->y), $zf - ($oz < 0)))) &&
				!StaticBlock::getIsSolid($this->entity->level->level->getBlockID($xf - ($ox < 0), (int)($this->entity->y) + 1, $zf - ($oz < 0)))
			||
				StaticBlock::getIsSolid(($d = $this->entity->level->level->getBlockID($xf - ($ox < 0), (int)($this->entity->y), ceil($zf)))) &&
				!StaticBlock::getIsSolid($this->entity->level->level->getBlockID($xf - ($ox < 0), (int)($this->entity->y) + 1, ceil($zf)))
		);
		
		while(self::$ADVANCED){
			if($this->isDangerous($a) || $this->isDangerous($b) || $this->isDangerous($c) || $this->isDangerous($d)){
				return false;
			}
			$id1 = $this->entity->level->level->getBlockID($xf, $this->entity->y + $oy - 1, $zf);
			if($this->isDangerous($id1)) return false;
			if(!StaticBlock::getIsSolid($id1)){
				$id2 = $this->entity->level->level->getBlockID($xf, $this->entity->y - 2, $zf);
				$id3 = $this->entity->level->level->getBlockID($xf, $this->entity->y - 3, $zf);
				$id4 = $this->entity->level->level->getBlockID($xf, $this->entity->y - 4, $zf);
				$s2 = StaticBlock::getIsSolid($id2);
				$s3 = StaticBlock::getIsSolid($id3);
				$s4 = StaticBlock::getIsSolid($id4);
				if($this->isDangerous($id2)) return false;
				if($s2) break; //i cant goto label which wasnt declared before
				
				if($this->isDangerous($id3)) return false;
				if($s3) break;
				
				if($this->isDangerous($id4)) return false;
				if($s4) break;
				
				if(!($this->entity instanceof Chicken)){
					return false;
				}
			}
			break;
		}
		if($camera) $this->faceEntity($ox, $oy, $oz);
		if($this->entity->knockbackTime <= 0 && $this->isRotationCompleted()){
		    $this->entity->moveEntityWithOffset($ox, $oy, $oz);
		}
		return true;
	}
	
	public function movementTick(){
		if($this->isJumping() && $this->jumpTimeout <= 0 && $this->entity->onGround){
			$this->jumpTimeout = 10;
			$this->entity->speedY = 0.40;
		}
		if($this->jumpTimeout > 0) --$this->jumpTimeout;
	}
	
	public function rotateTick(){ //TODO handle more rotation
		$this->entity->lastHeadYaw = $this->entity->headYaw;
		$w180 = Utils::wrapAngleTo180($this->finalHeadYaw - $this->entity->headYaw); 
		$w180min = min(abs($w180), 20)*Utils::getSign($w180);
		$this->entity->headYaw = Utils::wrapAngleTo360($this->entity->headYaw + $w180min);
	}
	
	public function moveTo($x, $y, $z, $camera = true){
		return $this->moveNonInstant($x - floor($this->entity->x), $y - floor($this->entity->y), $z - floor($this->entity->z), $camera);
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

