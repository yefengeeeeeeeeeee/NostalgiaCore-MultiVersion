<?php


class MCBlockedProvider implements IBlockedProvider
{
	public function isBlocked(PathTile $tile)
	{
		return $tile instanceof PathTileXYZ && StaticBlock::getIsSolid($tile->level->level->getBlockID($tile->x, $tile->y, $tile->z));
	}

}

