<?php
/**
 * No sqrt distance
 */
class Pythagoras3D implements IDistanceAlgorithm
{
	public function calculate(PathTile $from, PathTile $to)
	{
		if($from instanceof PathTileXYZ && $to instanceof PathTileXYZ){
			return sqrt(($to->x - $from->x) * ($to->x - $from->x) + ($to->y - $from->y) * ($to->y - $from->y) + ($to->z - $from->z) * ($to->z - $from->z));
		}
		return INF;
	}

}

