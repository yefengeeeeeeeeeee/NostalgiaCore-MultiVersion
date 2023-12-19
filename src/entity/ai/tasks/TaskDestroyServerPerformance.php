<?php

class TaskDestroyServerPerformance extends TaskTempt
{
	public $attackTime = 0;
	public $server;
	public function __construct(){
		parent::__construct();
		$this->server = ServerAPI::request();
	}
	
	public function onStart(EntityAI $ai)
	{
		$this->selfCounter = 1;
	}
	
	public function onUpdate(EntityAI $ai)
	{
		if(!($this->target instanceof Entity) || ($this->target instanceof Entity && !$this->target->isPlayer()) || (Utils::distance_noroot($this->target, $ai->entity) > 256) || $this->target->level->getName() != $ai->entity->level->getName()){
			$this->reset();
			return;
		}
		if(Utils::distance_noroot($this->target, $ai->entity) > 100){
			$ai->mobController->moveTo($this->target->x, floor($ai->entity->y), $this->target->z, false);
		}else{
			
		}
		$ai->mobController->lookOn($this->target);
		
		if(--$this->attackTime <= 0){
			$this->rangedAttack($ai->entity, $this->target);
			$this->attackTime = 60;
		}
	}
	
	/**
	 * 
	 * @param Entity $selfEntity
	 * @param Entity $target
	 */
	public function rangedAttack($selfEntity, $target){
		$d = [
			"x" => $selfEntity->x,
			"y" => $selfEntity->y + 1.6,
			"z" => $selfEntity->z,
			"yaw" => $selfEntity->yaw,
			"pitch" => $selfEntity->pitch
		];
		/**
		 * @var Arrow $arrow
		 */
		$arrow = $this->server->api->entity->add($selfEntity->level, ENTITY_OBJECT, OBJECT_ARROW, $d);
		$arrow->shotByEntity = true;
		$arrow->shooterEID = $selfEntity->eid;
		$posY = ($target->y + $target->getEyeHeight() - 0.1);
		$diffX = $target->x - $selfEntity->x;
		$diffY = ($target->boundingBox->minY + ($target->height / 3)) - $posY;
		$diffZ = $target->z - $selfEntity->z;
		$v12 = sqrt($diffX * $diffX + $diffZ * $diffZ);
		if($v12 >= 1.0e-7){
			$yaw = ((atan2($diffZ, $diffX) * 180) / M_PI) - 90;
			$pitch = -((atan2($diffY, $v12) * 180) / M_PI);
			$v16 = $diffX / $v12;
			$v18 = $diffZ / $v12;
			$arrow->x = $selfEntity->x + $v16;
			$arrow->y = $posY;
			$arrow->z = $selfEntity->z + $v18;
			$arrow->yaw = $yaw;
			$arrow->pitch = $pitch;
			$arrow->updateAABB();
			$v20 = $v12 * 0.2;
			$arrow->shoot($diffX, $diffY + $v20, $diffZ, 1.6, 12);
			$this->server->api->entity->spawnToAll($arrow);
		}
	}
	
	public function canBeExecuted(EntityAI $ai)
	{
		$target = $this->findTarget($ai->entity, 16);
		if(($ai->entity instanceof Spider && !$ai->entity->level->isDay()) || $target instanceof Entity && $target->class === ENTITY_PLAYER && $target->isPlayer()){
			$this->target = $target; //TODO get rid of it
			$ai->entity->target = $target;
			return true;
		}
		
		return false;
	}
}

