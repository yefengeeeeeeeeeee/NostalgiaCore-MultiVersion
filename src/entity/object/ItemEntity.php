<?php

class ItemEntity extends Entity{
	const TYPE = "itemSpecial";
	const CLASS_TYPE = ENTITY_ITEM;
	public static $searchRadiusX = 0.5, $searchRadiusY = 0.0, $searchRadiusZ = 0.5;
	
	public $meta, $stack;
	
	public function __construct(Level $level, $eid, $class, $type = 0, $data = array())
	{
		parent::__construct($level, $eid, $class, $type, $data);
		$this->setSize(0.25, 0.25);
		$this->yOffset = $this->height / 2;
		if(isset($data["item"]) and ($data["item"] instanceof Item)){
			$this->meta = $this->data["item"]->getMetadata();
			$this->stack = $this->data["item"]->count;
		} else{
			$this->meta = (int) $this->data["meta"];
			$this->stack = (int) $this->data["stack"];
		}
		$this->hasGravity = true;
		$this->setHealth(5, "generic");
		$this->gravity = 0.04;
		$this->delayBeforePickup = 20;
		$this->stepHeight = 0;
	}
	
	public function counterUpdate(){
		parent::counterUpdate();
		if($this->delayBeforePickup > 0) --$this->delayBeforePickup;
	}
	
	public function searchForOtherItemsNearby(){
		$ents = $this->level->getEntitiesInAABBOfType($this->boundingBox->expand(self::$searchRadiusX, self::$searchRadiusY, self::$searchRadiusZ), ENTITY_ITEM);
		
		foreach($ents as $e){
			$this->tryCombining($e);
		}
		
	}
	
	public function spawn($player)
	{
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
	}
	
	
	public function tryCombining(Entity $another){
		
		if($another->eid == $this->eid) return false;
		
		if(!$another->closed && !$this->closed){
			if($another->type == $this->type && $another->meta == $this->meta){
				if(($another->stack + $this->stack) > 64) return false; //TODO dynamic stack size
				
				$another->stack += $this->stack;
				$another->age = min($this->age, $another->age);
				$this->close();
				//TODO respawn another entity?
			}
		}
	}
	
	public function updateEntityMovement(){
		$this->speedY -= 0.04;
		$this->noClip = false; //TODO pushOutofBlocks
		$this->move($this->speedX, $this->speedY, $this->speedZ);
		
		$var1 = (int)$this->x != (int)$this->lastX || (int)$this->y != (int)$this->lastY || (int)$this->z != (int)$this->lastZ;
		
		if($var1 || $this->counter % 25 == 0){
			$blockIDAt = $this->level->level->getBlockID(floor($this->x), floor($this->y), floor($this->z));
			if($blockIDAt == LAVA || $blockIDAt == STILL_LAVA){
				$this->speedY = 0.2;
				$this->speedX = (lcg_value() - lcg_value()) * 0.2;
				$this->speedZ = (lcg_value() - lcg_value()) * 0.2;
			}
			
			$this->searchForOtherItemsNearby(); //not in vanilla 0.8.1
		}
		
		if($this->closed) return;
		
		$friction = 0.98;
		if($this->onGround){
			$friction = 0.588;
			$v3 = $this->level->level->getBlockID($this->x, floor($this->boundingBox->minY) - 1, $this->z);
			if($v3 > 0) $friction = StaticBlock::getSlipperiness($v3);
		}
		
		$this->speedX *= $friction;
		$this->speedY *= 0.98;
		$this->speedZ *= $friction;
		
		if($this->onGround) $this->speedY *= -0.5;
		
		++$this->age;
		//TODO despawn after age >= 6000 ?; 
	}
}
