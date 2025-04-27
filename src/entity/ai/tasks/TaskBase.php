<?php

abstract class TaskBase
{
	public $isStarted = false, $selfCounter = 0;

	public function __construct(){}

	/**
	 * Executed when entity starts task. It is recommended to set counter here.
	 */
	abstract function onStart(EntityAI $ai);

	/**
	 * Every tick until counter will be not equal to 0. It is recommended to update value of counter here.
	 */
	abstract function onUpdate(EntityAI $ai);

	/**
	 * On task end.
	 */
	abstract function onEnd(EntityAI $ai);

	/**
	 * Can start the task
	 */
	abstract function canBeExecuted(EntityAI $ai);

	/**
	 * @return number ticks
	 */
	function getIdleTimeAfterEnd(){
		return 0;
	}

	function reset(){
		$this->isStarted = false;
		$this->selfCounter = 0;
	}

	final function __toString(){
		return get_class($this);
	}
}

