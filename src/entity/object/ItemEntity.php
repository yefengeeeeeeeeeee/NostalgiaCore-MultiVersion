<?php

class ItemEntity extends Entity{
	const TYPE = "itemSpecial";
	const CLASS_TYPE = ENTITY_ITEM;
	
	public $meta, $stack;
	
	public function __construct(Level $level, $eid, $class, $type = 0, $data = array())
	{
		parent::__construct($level, $eid, $class, $type, $data);
		$this->setSize(0.25, 0.25);
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
	}
}
