<?php

class TaskEatTileGoal extends TaskBase
{
	public function onStart(EntityAI $ai)
	{
		$this->selfCounter = 40;
		$ai->entity->server->api->player->broadcastPacket($ai->entity->level->players, new EntityEventPacket($ai->entity->eid, EntityEventPacket::ENTITY_ANIM_10));
	}

	public function onEnd(EntityAI $ai)
	{
		
	}

	public function onUpdate(EntityAI $ai)
	{
		if (--$this->selfCounter === 4)
		{
			$id = $ai->entity->level->getBlock($ai->entity->floor());
			$idb = $ai->entity->level->getBlock($id->getSide(0));
			if($id->getID() === TALL_GRASS){
				$ai->entity->level->setBlock($idb, BlockAPI::get(AIR));
				$ai->entity->eatGrass();
				
			}elseif($idb->getID() === GRASS){
				$ai->entity->level->setBlock($idb, BlockAPI::get(DIRT));
				$ai->entity->eatGrass();
			}
		}
	}

	public function canBeExecuted(EntityAI $ai)
	{
		if($ai->isStarted("TaskRandomWalk")) return false;
		if(mt_rand(0, ($ai->entity instanceof Ageable && $ai->entity->isBaby()) ? 50 : 1000) == 0){
			$b = $ai->entity->level->getBlock($ai->entity->floor());
			$bu = $ai->entity->level->getBlock($b->getSide(0));
			return ($b->getID() === TALL_GRASS && $b->getMetadata() === 1) || $bu->getID() === GRASS;
		}
		return false;
		//this.theEntity.getRNG().nextInt(this.theEntity.isChild() ? 50 : 1000) != 0
	}

}

