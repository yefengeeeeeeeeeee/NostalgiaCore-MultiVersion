<?php

class TaskRandomWalk extends TaskBase
{
	private $x, $z;
	public $moveSpeed;
	public function __construct($moveSpeed){
		$this->moveSpeed = $moveSpeed;
	}
	
	public function onStart(EntityAI $ai)
	{
		$this->x = mt_rand(-1, 1);
		$this->z = mt_rand(-1, 1);
		if($this->x === 0 && $this->z === 0){
			$this->reset();
			return false;
		}
		$this->selfCounter = mt_rand(80, 100);
	}

	public function onEnd(EntityAI $ai)
	{
		$this->x = $this->z = 0;
	}

	public function onUpdate(EntityAI $ai)
	{
		if(($ai->entity instanceof Creeper && $ai->entity->isIgnited()) || $ai->isStarted("TaskTempt")) {
			$this->reset();
			return false; //TODO Better way: block movement
		}
		--$this->selfCounter;
		$ai->mobController->setMovingOffset($this->x, 0, $this->z, $this->moveSpeed);
		//TODO fix $ai->mobController->moveNonInstant($this->x, 0, $this->z);
	}

	public function canBeExecuted(EntityAI $ai)
	{
		return true;
		//if(($ai->entity instanceof Creeper && $ai->entity->isIgnited()) || $ai->entity->hasPath() || $ai->isStarted("TaskTempt") ||$ai->isStarted("TaskAttackPlayer")) {
		//	return false;
		//} // i really need mutexBits
		//return !$ai->entity->inPanic && !$ai->isStarted("TaskMate")  && !$ai->isStarted("TaskEatTileGoal") && !$ai->isStarted("TaskLookAround") && !$ai->isStarted("TaskLookAtPlayer") && mt_rand(0, 120) == 0;
	}

	
}
