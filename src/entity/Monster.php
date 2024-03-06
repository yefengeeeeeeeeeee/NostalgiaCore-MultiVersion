<?php
class Monster extends Creature{
	public function __construct(Level $level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		//$this->ai->addTask(new TaskAttackPlayer());
	}
	
	public function getAttackDamage(){
		return 2;
	}

	public function attackEntity($entity, $distance){
		$entity->harm($this->getAttackDamage(), $this->eid);
		return true;
	}
}
