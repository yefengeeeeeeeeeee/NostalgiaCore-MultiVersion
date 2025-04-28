<?php

class PaintingItem extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(PAINTING, 0, $count, "Painting");
		$this->isActivable = true;
	}
	public static $motives = [
		// Motive Width Height
		"Kebab" => [1, 1],
		"Aztec" => [1, 1],
		"Alban" => [1, 1],
		"Aztec2" => [1, 1],
		"Bomb" => [1, 1],
		"Plant" => [1, 1],
		"Wasteland" => [1, 1],
		"Wanderer" => [1, 2],
		"Graham" => [1, 2],
		"Pool" => [2, 1],
		"Courbet" => [2, 1],
		"Sunset" => [2, 1],
		"Sea" => [2, 1],
		"Creebet" => [2, 1],
		"Match" => [2, 2],
		"Bust" => [2, 2],
		"Stage" => [2, 2],
		"Void" => [2, 2],
		"SkullAndRoses" => [2, 2],
		//"Wither" => array(2, 2),
		"Fighters" => [4, 2],
		"Skeleton" => [4, 3],
		"DonkeyKong" => [4, 3],
		"Pointer" => [4, 4],
		"Pigscene" => [4, 4],
		"BurningSkull" => [4, 4],
	];
	private static $direction = [2, 0, 1, 3];
	private static $right = [4, 5, 3, 2];

	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		if($target->isTransparent === false and $face > 1 and $block->isSolid === false){
			$server = ServerAPI::request();

			if($face < 2 || $face > 5) return;
			$data = [
				"x" => $target->x,
				"y" => $target->y,
				"z" => $target->z,
				"Direction" => match($face){
					2 => 2,
					3 => 0,
					4 => 1,
					default => 3
				},
				"xPos" => $target->x,
				"yPos" => $target->y,
				"zPos" => $target->z,
			];

			$painting = new Painting($level, 0, ENTITY_OBJECT, OBJECT_PAINTING, $data);
			if(!$painting->isValid){
				$player->sendInventory(); //force resync
				return false;
			}
			$painting->eid = $server->api->entity->getNextEID();
			$server->api->entity->addRaw($painting);
			$server->api->entity->spawnToAll($painting);
			if(($player->gamemode & 0x01) === 0x00){
				$player->removeItem($this->getID(), $this->getMetadata(), 1, false);
			}
			return true;
		}
		return false;
	}

}