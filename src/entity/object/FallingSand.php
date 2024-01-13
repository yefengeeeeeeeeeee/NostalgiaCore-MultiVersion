<?php

class FallingSand extends Entity{
	const TYPE = FALLING_SAND;
	const CLASS_TYPE = ENTITY_FALLING;
	
	public $fallTime = 0;
	
	public function __construct($level, $eid, $class, $type = 0, $data = []){
		parent::__construct($level, $eid, $class, $type, $data);
		$this->setHealth(PHP_INT_MAX, "generic");
		$this->height = 0.98;
		$this->width = 0.98;
		$this->yOffset = $this->height / 2;
		$this->hasGravity = true;
		$this->gravity = 0.04;
	}
	
	public function update($now){
		if($this->closed) return;
		
		if( $this->data["Tile"] == AIR) $this->close();
		else {
			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;
			++$this->fallTime;
			$this->speedY -= 0.04;
			$this->move($this->speedX, $this->speedY, $this->speedZ);
			
			$this->speedX *= 0.98;
			$this->speedY *= 0.98;
			$this->speedZ *= 0.98;
			
			$x = floor($this->x);
			$y = floor($this->y);
			$z = floot($this->z);
			
			if($this->fallTime == 1){
				/* i dont think this is needed here?
				 * if (this.worldObj.getBlockId(var1, var2, var3) != this.blockID)
                    {
                        this.setDead();
                        return;
                    }

                    this.worldObj.setBlockToAir(var1, var2, var3);
				 */
			}
			
			if($this->onGround){
				$this->speedX *= 0.7;
				$this->speedZ *= 0.7;
				$this->speedY *= -0.5;
				
				//1.5.2 has piston check, skipping
				$this->close();
				//if (!this.isBreakingAnvil && this.worldObj.canPlaceEntityOnSide(this.blockID, var1, var2, var3, true, 1, (Entity)null, (ItemStack)null) && !BlockSand.canFallBelow(this.worldObj, var1, var2 - 1, var3) && this.worldObj.setBlock(var1, var2, var3, this.blockID, this.metadata, 3))
				//	
				//}
				//a check if block has tilentity, might be added after tile entities rewrite
				
			}
		}
	}
}