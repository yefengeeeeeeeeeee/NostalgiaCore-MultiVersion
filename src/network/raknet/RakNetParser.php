<?php

class RakNetParser{

	public $packet;
	private $buffer;
	private $source;
	private $offset;

	public function __construct(&$buffer, $source){
		$this->source = &$source;
		$this->buffer = &$buffer;
		$this->offset = 0;
		if(strlen($this->buffer) > 0){
			$this->parse();
		}else{
			$this->packet = false;
		}
	}

	private function parse(){
		$this->packet = new RakNetPacket(ord($this->get(1)));
		$this->packet->buffer = &$this->buffer;
		$this->packet->length = strlen($this->buffer);
		switch($this->packet->pid()){
			case RakNetInfo::UNCONNECTED_PING:
			case RakNetInfo::UNCONNECTED_PING_OPEN_CONNECTIONS:
				$this->packet->pingID = $this->getLong();
				$this->offset += 16; //Magic
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_1:
				$this->offset += 16; //Magic
				$this->packet->structure = $this->getByte();
				$this->packet->mtuSize = strlen($this->get(true));
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_2:
				$this->offset += 16; //Magic
				$this->packet->security = $this->get(5);
				$this->packet->port = $this->getShort(false);
				$this->packet->mtuSize = $this->getShort(false);
				$this->packet->clientID = $this->getLong();
				break;
			case RakNetInfo::DATA_PACKET_0:
			case RakNetInfo::DATA_PACKET_1:
			case RakNetInfo::DATA_PACKET_2:
			case RakNetInfo::DATA_PACKET_3:
			case RakNetInfo::DATA_PACKET_4:
			case RakNetInfo::DATA_PACKET_5:
			case RakNetInfo::DATA_PACKET_6:
			case RakNetInfo::DATA_PACKET_7:
			case RakNetInfo::DATA_PACKET_8:
			case RakNetInfo::DATA_PACKET_9:
			case RakNetInfo::DATA_PACKET_A:
			case RakNetInfo::DATA_PACKET_B:
			case RakNetInfo::DATA_PACKET_C:
			case RakNetInfo::DATA_PACKET_D:
			case RakNetInfo::DATA_PACKET_E:
			case RakNetInfo::DATA_PACKET_F:
				$this->packet->seqNumber = $this->getLTriad();
				$this->packet->data = [];
				$this->packet->ip = $this->source;
				$PROTOCOL = PlayerAPI::decodeProtocol($this->packet->ip);

				while(!$this->feof() and ($pk = $this->parseDataPacket($PROTOCOL)) instanceof RakNetDataPacket){
					$this->packet->data[] = $pk;
				}
				break;
			case RakNetInfo::NACK:
			case RakNetInfo::ACK:
				$count = $this->getShort();
				$this->packet->packets = [];
				for($i = 0; $i < $count and !$this->feof(); ++$i){
					if($this->getByte() === 0){
						$start = $this->getLTriad();
						$end = $this->getLTriad();
						if(($end - $start) > 4096){
							$end = $start + 4096;
						}
						for($c = $start; $c <= $end; ++$c){
							$this->packet->packets[] = $c;
						}
					}else{
						$this->packet->packets[] = $this->getLTriad();
					}
				}
				break;
			default:
				$this->packet = false;
				break;
		}
	}

	private function get($len){
		if($len <= 0){
			$this->offset = strlen($this->buffer) - 1;
			return "";
		}
		if($len === true){
			return substr($this->buffer, $this->offset);
		}
		$this->offset += $len;
		return substr($this->buffer, $this->offset - $len, $len);
	}

	private function getLong($unsigned = false){
		return Utils::readLong($this->get(8), $unsigned);
	}

	private function getByte(){
		return ord($this->get(1));
	}

	private function getShort($unsigned = false){
		return Utils::readShort($this->get(2), $unsigned);
	}

	private function getLTriad(){
		return Utils::readTriad(strrev($this->get(3)));
	}

	private function feof(){
		return !isset($this->buffer[$this->offset]);
	}

	private function parseDataPacket($protocol = ProtocolInfo::CURRENT_PROTOCOL){
		$packetFlags = $this->getByte();
		$reliability = ($packetFlags & 0b11100000) >> 5;
		$hasSplit = ($packetFlags & 0b00010000) > 0;
		$length = (int) ceil($this->getShort() / 8);
		
		if($reliability === 2
			or $reliability === 3
			or $reliability === 4
			or $reliability === 6
			or $reliability === 7){
			$messageIndex = $this->getLTriad();
		}else{
			$messageIndex = false;
		}

		if($reliability === 1
			or $reliability === 3
			or $reliability === 4
			or $reliability === 7){
			$orderIndex = $this->getLTriad();
			$orderChannel = $this->getByte();
		}else{
			$orderIndex = false;
			$orderChannel = false;
		}

		if($hasSplit){
			$splitCount = $this->getInt();
			$splitID = $this->getShort();
			$splitIndex = $this->getInt();
		}else{
			$splitCount = false;
			$splitID = false;
			$splitIndex = false;
		}

		if($length <= 0
			or $orderChannel >= 32
			or ($hasSplit === true and $splitIndex >= $splitCount)){
			return false;
		}else{
			$pid = $this->getByte();
			$buffer = $this->get($length - 1);
			if(strlen($buffer) < ($length - 1)){
				return false;
			}

			$data = PacketPool::getPacket($pid, $protocol);
			$data->reliability = $reliability;
			$data->hasSplit = $hasSplit;
			$data->messageIndex = $messageIndex;
			$data->orderIndex = $orderIndex;
			$data->orderChannel = $orderChannel;
			$data->splitCount = $splitCount;
			$data->splitID = $splitID;
			$data->splitIndex = $splitIndex;
			$data->setBuffer($buffer);
		}
		return $data;
	}

	private function getInt($unsigned = false){
		return Utils::readInt($this->get(4), $unsigned);
	}

}
