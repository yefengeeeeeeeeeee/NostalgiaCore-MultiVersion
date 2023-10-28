<?php

class MCDiagonalProvider implements INeighborProvider
{
	private static $neighbors = array(
		[ -1, 0, -1 ], [-1, 0, 0], [-1, 0, 1],
		[0, 0, -1], [0, 0, 1],
		[1, 0, -1], [1, 0, 0], [1, 0, -1]
	);
	private static $jumpOffset = [0, 1, 0];
	private static $moveDownOffset = [0, -1, 0];
	public function getNeighbors(PathTile $tile)
	{
		if($tile instanceof PathTileXYZ){
			$pnts = [];
			foreach(MCDiagonalProvider::$neighbors as $offset){
				$pnt = $tile->addOffset($offset);
				if(!StaticBlock::getIsSolid($tile->level->level->getBlockID($pnt->x, $pnt->y, $pnt->z))){
					if(!StaticBlock::getIsSolid($tile->level->level->getBlockID($pnt->x, $pnt->y - 1, $pnt->z))){
						--$pnt->y;
					}
					$pnts[] = $pnt;
				}else{
					if(!StaticBlock::getIsSolid($tile->level->level->getBlockID($pnt->x, $pnt->y + 1, $pnt->z))){
						++$pnt->y;
						$pnts[] = $pnt;
					}
				}
			}
			return $pnts;
		}
		return [];
	}

	
}

