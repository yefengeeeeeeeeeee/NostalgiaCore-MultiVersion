<?php

class RakNetParser{
	public static function parse(&$buffer, $source, $port){
		$offset = 0;
		++$offset;
		$packet = new RakNetPacket(ord(substr($buffer, $offset - 1, 1)));
        $packet->source = &$source;
		$packet->buffer = &$buffer;
        $packet->port = &$port;
		$packet->length = strlen($buffer);
		switch($packet->pid()){
			case RakNetInfo::UNCONNECTED_PING:
			case RakNetInfo::UNCONNECTED_PING_OPEN_CONNECTIONS:
				$offset += 8;
				$packet->pingID = Utils::readLong(substr($buffer, $offset - 8, 8), false);
				$offset += 16; //Magic
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_1:
				$offset += 16; //Magic
				++$offset;
				$packet->structure = ord(substr($buffer, $offset - 1, 1));
				$packet->mtuSize = strlen(substr($buffer, $offset));
				break;
			case RakNetInfo::OPEN_CONNECTION_REQUEST_2:
				$offset += 16; //Magic
				$offset += 5;
				$packet->security = Utils::readLong(substr($buffer, $offset - 5, 5), false);
				$offset += 2;
				$packet->port = Utils::readShort(substr($buffer, $offset - 2, 2), false);
				$offset += 2;
				$packet->mtuSize = Utils::readShort(substr($buffer, $offset - 2, 2), false);
				$offset += 8;
				$packet->clientID = Utils::readLong(substr($buffer, $offset - 8, 8), false);
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
				$offset += 3;
				$packet->seqNumber = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
				$packet->data = [];
                $packet->ip = $packet->source;
                $packet->port = $packet->port;
                $PROTOCOL = PlayerAPI::decodeProtocol($packet->ip, $packet->port);
				
				while(isset($buffer[$offset]) and ($pk = static::parseDataPacket($offset, $buffer, $PROTOCOL)) instanceof RakNetDataPacket){
					$packet->data[] = $pk;
				}
				break;
			case RakNetInfo::NACK:
			case RakNetInfo::ACK:
				$offset += 2;
				$count = Utils::readShort(substr($buffer, $offset - 2, 2), false);
				$packet->packets = [];
				for($i = 0; $i < $count && isset($buffer[$offset]); ++$i){
					++$offset;
					if(ord(substr($buffer, $offset - 1, 1)) === 0){
						$offset += 3;
						$start = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
						$offset += 3;
						$end = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
						if(($end - $start) > 4096){
							$end = $start + 4096;
						}
						for($c = $start; $c <= $end; ++$c){
							$packet->packets[] = $c;
						}
					}else{
						$offset += 3;
						$packet->packets[] = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
					}
				}
				break;
			default:
				$packet = false;
				break;
		}
		return $packet;
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

	private function getByte(){
		return ord($this->get(1));
	}

	private function getShort($unsigned = false){
		return ;
	}

	private function getLTriad(){
		return Utils::readTriad(strrev($this->get(3)));
	}

	private static function parseDataPacket(&$offset, &$buffer, $protocol = ProtocolInfo::CURRENT_PROTOCOL){
		++$offset;
		$packetFlags = ord(substr($buffer, $offset - 1, 1));
		$reliability = ($packetFlags & 0b11100000) >> 5;
		$hasSplit = ($packetFlags & 0b00010000) > 0;
		$offset += 2;
		$length = (int) ceil(Utils::readShort(substr($buffer, $offset - 2, 2), false) / 8);
		
		if($reliability == 2 ||$reliability == 3 || $reliability == 4 || $reliability == 6 || $reliability == 7){
			$offset += 3;
			$messageIndex = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
		}else{
			$messageIndex = false;
		}
		if($reliability == 1 || $reliability == 4){
			$offset += 3;
			$seqIndex = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
		}else{
			$seqIndex = false;
		}

		if($reliability == 1 ||$reliability == 3 || $reliability == 4 || $reliability == 7){
			$offset += 3;
			$orderIndex = Utils::readTriad(strrev(substr($buffer, $offset - 3, 3)));
			++$offset;
			$orderChannel = ord(substr($buffer, $offset - 1, 1));
		}else{
			$orderIndex = false;
			$orderChannel = false;
		}

		if($hasSplit){
			$offset += 4;
			$splitCount = Utils::readInt(substr($buffer, $offset - 4, 4), false);
			$offset += 2;
			$splitID = Utils::readShort(substr($buffer, $offset - 2, 2), false);
			$offset += 4;
			$splitIndex = Utils::readInt(substr($buffer, $offset - 4, 4), false);
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
			++$offset;
			$pid = ord(substr($buffer, $offset - 1, 1));
			$offset += $length-1;
			$buf = substr($buffer, $offset - ($length - 1), ($length - 1));
			if(strlen($buf) < ($length - 1)){
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
			$data->seqIndex = $seqIndex;
			$data->localEids = true;
			$data->setBuffer($buf);
		}
		return $data;
	}
	
	/**
	 * @deprecated
	 */
	public $packet;
	/**
	 * @deprecated use RakNetParser::parse
	 */
	public function __construct(&$buffer){
		if(strlen($buffer) > 0){
			$this->packet = static::parse($buffer);
		}else{
			$this->packet = false;
		}
	}
}