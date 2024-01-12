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
		
		$this->entity->ai->mobController->setMovingTarget($this->entity->currentNode->x, $this->entity->currentNode->y, $this->entity->currentNode->z, 0.25);
		console($this->entity->currentNode.":".$this->entity->boundingBox);
		if($this->entity->boundingBox->isXYZInsideNS($this->entity->currentNode->x, $this->entity->currentNode->y, $this->entity->currentNode->z)){
			++$this->entity->currentIndex;
			console("next");
			$this->entity->currentNode = null;
		}
	}
	
}

