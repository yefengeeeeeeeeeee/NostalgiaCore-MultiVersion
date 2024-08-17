<?php

class Painting extends Entity{
	const CLASS_TYPE = ENTITY_OBJECT;
	const TYPE = OBJECT_PAINTING;
	public function __construct(Level $level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->x = $this->data["TileX"] ?? $this->x;
		$this->y = $this->data["TileY"] ?? $this->y;
		$this->z = $this->data["TileZ"] ?? $this->z;
		$this->setHealth(1, "generic");
		$this->canBeAttacked = true;
		$this->width = 1;
		$this->isStatic = true;
	}
	
	public function getDrops(){
		return [
			[PAINTING, 0, 1]
		];
	}
	
	public function isPickable(){
		return true;
	}
	
	public function survives(){
		
	}
	
	public function createSaveData(){
		$data = parent::createSaveData();
		
		$data["Motive"] = $this->data["Motive"];
		
		return $data;
	}
	
	public function spawn($player){
		$pk = new AddPaintingPacket;
		$pk->eid = $this->eid;
		$pk->x = (int) $this->x;
		$pk->y = (int) $this->y;
		$pk->z = (int) $this->z;
		$pk->direction = $this->getDirection();
		$pk->title = $this->data["Motive"];
		$player->dataPacket($pk);
	}
}
