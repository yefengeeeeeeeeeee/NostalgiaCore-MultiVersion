<?php

class WoolBlock extends SolidBlock{
	public static $blockID;
	public function __construct($meta = 0){
		parent::__construct(WOOL, $meta, "Wool");
		$names = [
			0 => "White Wool",
			1 => "Orange Wool",
			2 => "Magenta Wool",
			3 => "Light Blue Wool",
			4 => "Yellow Wool",
			5 => "Lime Wool",
			6 => "Pink Wool",
			7 => "Gray Wool",
			8 => "Light Gray Wool",
			9 => "Cyan Wool",
			10 => "Purple Wool",
			11 => "Blue Wool",
			12 => "Brown Wool",
			13 => "Green Wool",
			14 => "Red Wool",
			15 => "Black Wool",
		];
		$this->name = $names[$this->meta];
		$this->hardness = 4;
	}

}