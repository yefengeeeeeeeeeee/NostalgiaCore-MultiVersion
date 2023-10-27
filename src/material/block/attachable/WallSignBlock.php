<?php

/***REM_START***/
require_once("SignPostBlock.php");
/***REM_END***/

class WallSignBlock extends SignPostBlock{
	
	public static $meta2side = [
		2 => 3,
		3 => 2,
		4 => 5,
		5 => 4
	];
	
	
	public function __construct($meta = 0){
		TransparentBlock::__construct(WALL_SIGN, $meta, "Wall Sign");
		$this->isSolid = false;
	}

	public function onUpdate($type){
		$attached = $this->getSide(self::$meta2side[$this->meta] ?? -1);
		if(!($attached instanceof Block) || (!$attached->isSolid && $attached->getID() != SIGN_POST && $attached->getID() != WALL_SIGN)){
			$this->level->setBlock($this, new AirBlock(), true, true, true);
			(ServerAPI::request())->api->entity->drop(new Position($this->x + 0.5, $this->y + 0.5, $this->z + 0.5, $this->level), BlockAPI::getItem(SIGN, 0, 1));
		}
		
		return false;
	}
}