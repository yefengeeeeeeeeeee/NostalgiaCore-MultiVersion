<?php

class FallingSand extends Entity{
	const TYPE = FALLING_SAND;
	const CLASS_TYPE = ENTITY_FALLING;
	public function __construct($level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->setHealth(PHP_INT_MAX, "generic");
		$this->height = 0.98;
		$this->width = 0.98;
		$this->hasGravity = true;
		$this->gravity = 0.04;
	}
}