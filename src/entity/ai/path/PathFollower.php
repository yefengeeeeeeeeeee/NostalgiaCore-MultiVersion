<?php

class PathFollower{
	/**
	 * @var Living
	 */
	public $entity;
	
	public function __construct(Living $entity){
		$this->entity = $entity;
	}
	
	public function followPath(){
		
		if(!isset($this->entity) || !($this->entity instanceof Living)) return;
		if(!$this->entity->hasPath()) return;
		
		if($this->entity->currentNode == null){
			$this->entity->currentNode = $this->entity->path[$this->entity->currentIndex];
		}
		
		$this->entity->ai->mobController->setMovingTarget($this->entity->currentNode->x + 0.5, $this->entity->currentNode->y, $this->entity->currentNode->z + 0.5, 1.0);
		if($this->entity->boundingBox->isXYZInsideNS($this->entity->currentNode->x + 0.5, $this->entity->currentNode->y, $this->entity->currentNode->z + 0.5)){
			++$this->entity->currentIndex;
			console("next");
			$this->entity->currentNode = null;
		}
		
		
		if($this->entity->currentIndex >= count($this->entity->path)){
			console("path finished.");
			$this->entity->currentNode = null;
			$this->entity->path = null;
			$this->entity->currentIndex = 0;
			
			foreach($this->entity->pathEIDS as $eid){
				$pk = new RemoveEntityPacket();
				$pk->eid = $eid;
				foreach($this->entity->level->players as $player){
					$player->dataPacket($pk);
				}
			}
			
		}
	}
	
}

