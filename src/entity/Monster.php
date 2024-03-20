<?php
class Monster extends Creature{
	public function __construct(Level $level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		//$this->ai->addTask(new TaskAttackPlayer());
	}
	
	public function getAttackDamage(){
		return 2;
	}
	/**
	 * 
	 * @see Entity::attackEntity()
	 */
	public function attackEntity($entity, $distance){
		if($distance < 2.0 && $entity->boundingBox->maxX > $this->boundingBox->minY && $entity->boundingBox->minY < $this->boundingBox->maxY){
			$entity->harm($this->getAttackDamage(), $this->eid);
			return true;
		}else{
			return false;
		}
	}
}
