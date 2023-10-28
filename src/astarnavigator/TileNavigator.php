<?php
class TileNavigator implements ITileNavigator
{
	private $blockedProvider, $neighborProvider, $distanceAlgorithm;

	public static $pathfinderAccessed = 0;

	public function __construct(IBlockedProvider $blockedProvider, INeighborProvider $neighborProvider, IDistanceAlgorithm $distanceAlgorithm){
		$this->blockedProvider = $blockedProvider;
		$this->neighborProvider = $neighborProvider;
		$this->distanceAlgorithm = $distanceAlgorithm;
	}
	
	public function reconstructPath($path, $current){
		$totalPath = [$current];
		while (isset($path[(string)$current]))
		{
			$current = $path[(string)$current];
			$totalPath[] = $current;
		}
		//foreach(array_unique($path) as $k => $p) console($k.":".$p);
		//foreach($totalPath as $k => $p) console($k.":".$p);
		array_pop($totalPath);
		return array_reverse($totalPath);
	}
	
	public function navigate(PathTile $from, PathTile $to, $maxDist)
	{
		$open = [0 => $from];
		//$open->insert(, 0);
		$path = [];
		$gScore = [];
		$gScore[(string) $from] = 0;
		$has = [(string)$from, true];
		if($this->blockedProvider->isBlocked($to)){
			return null;
		}
		$visited = [];
		$maxDist*=$maxDist; //no square root
		while(count($open) > 0)
		{
			$current = array_pop($open);
			if ($current == $to){
				return $this->reconstructPath($path, $current);
			}
			foreach($this->neighborProvider->getNeighbors($current) as $neighbor)
			{
				$dist = ($from->x - $neighbor->x)*($from->x - $neighbor->x) + ($from->y - $neighbor->y)*($from->y - $neighbor->y) + ($from->z - $neighbor->z)*($from->z - $neighbor->z);
				if($dist < -$maxDist || $dist > $maxDist){ //Utils::distance_noroot($neighbor->asArray(), $from->asArray())
					continue;
				}
				if(isset($visited[(string)$neighbor])){
					continue;
				}
				
				$visited[(string)$neighbor] = $neighbor;
				$distbetweenCost = $this->distanceAlgorithm->calculate($current, $neighbor);
				$tentativeG = $gScore[(string) $current] + $distbetweenCost;
				if (!isset($has[(string)$neighbor]))
				{
					$open[-$tentativeG] = $neighbor;
					$has[(string)$neighbor] = true;
				}
				elseif ($tentativeG >= $gScore[(string) $neighbor])
				{
					continue;
				}
				if(!isset($gScore[(string) $neighbor]) || $distbetweenCost < $gScore[(string) $neighbor]){
					$path[(string) $neighbor] = $current;
				}
				
				$gScore[(string) $neighbor] = $tentativeG;
			}
		}
		
		return null;
	}

	
}

