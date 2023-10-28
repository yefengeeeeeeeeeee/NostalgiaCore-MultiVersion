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
		$this->width = 0.98;
		$this->height = 0.7;
		$this->update();
	}
	
	public function getDrops(){
		return [
			[MINECART, 0, 1]
		];
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
		if($action === InteractPacket::ACTION_HOLD && $e->isPlayer() && $this->canRide($e)){
			$this->linkedEntity = $e;
			$e->isRiding = true;
			$this->linkEntity($e, SetEntityLinkPacket::TYPE_RIDE);
			return true;
		}
		if($e->isPlayer() && $action === InteractPacket::ACTION_HOLD){
			$this->linkEntity($e, SetEntityLinkPacket::TYPE_REMOVE);
			$this->linkedEntity = 0;
			$e->isRiding = false;
			return true;
		}
		parent::interactWith($e, $action);
	}
	public function canRide($e)
	{
	   return !($this->linkedEntity instanceof Entity) && !$e->isRiding;
	}

}
