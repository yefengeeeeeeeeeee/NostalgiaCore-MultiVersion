<?php

class ItemEntity extends Entity{
	const TYPE = "itemSpecial";
	const CLASS_TYPE = ENTITY_ITEM;
	
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
	
	public function updateEntityMovement(){
		//TODO it is weird on client side for some reason
		if(abs($this->speedX) < self::MIN_POSSIBLE_SPEED) $this->speedX = 0;
		if(abs($this->speedZ) < self::MIN_POSSIBLE_SPEED) $this->speedZ = 0;
		if(abs($this->speedY) < self::MIN_POSSIBLE_SPEED) $this->speedY = 0;
		
		$this->speedY -= 0.04;
		$this->noClip = false; //TODO pushOutofBlocks
		$this->move($this->speedX, $this->speedY, $this->speedZ);
		
		$var1 = (int)$this->x != (int)$this->lastX || (int)$this->y != (int)$this->lastY || (int)$this->z != (int)$this->lastZ;
		if($var1/* || $this->ticksExisted % 25 == 0*/){ //TODO ticksExisted(should be alternative in nc already)
			//TODO check material
			
			//TODO search for other items
		}
		
		$friction = 0.98;
		
		if($this->onGround){
			$friction = 0.588;
			$v3 = $this->level->level->getBlockID($this->x, floor($this->boundingBox->minY) - 1, $this->z);
			if($v3 > 0) $friction = StaticBlock::getSlipperiness($v3);
		}
		
		$this->speedX *= $friction;
		$this->speedY *= 0.98;
		$this->speedZ *= $friction;
		
		//if($this->onGround) $this->speedY *= -0.5;
		
		//TODO ++age; despawn after age >= 6000; 
	}
}
