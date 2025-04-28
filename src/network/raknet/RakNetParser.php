<?php

class RakNetParser{

	public $packet;
	private $buffer;
	private $offset;

	public function __construct(&$buffer){
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
				while(!$this->feof() and ($pk = $this->parseDataPacket()) instanceof RakNetDataPacket){
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

	private function parseDataPacket(){
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

			$data = match($pid){
				ProtocolInfo::PING_PACKET => new PingPacket(),
				ProtocolInfo::PONG_PACKET => new PongPacket(),
				ProtocolInfo::CLIENT_CONNECT_PACKET => new ClientConnectPacket(),
				ProtocolInfo::SERVER_HANDSHAKE_PACKET => new ServerHandshakePacket(),
				ProtocolInfo::DISCONNECT_PACKET => new DisconnectPacket(),
				ProtocolInfo::LOGIN_PACKET => new LoginPacket(),
				ProtocolInfo::LOGIN_STATUS_PACKET => new LoginStatusPacket(),
				ProtocolInfo::READY_PACKET => new ReadyPacket(),
				ProtocolInfo::MESSAGE_PACKET => new MessagePacket(),
				ProtocolInfo::SET_TIME_PACKET => new SetTimePacket(),
				ProtocolInfo::START_GAME_PACKET => new StartGamePacket(),
				ProtocolInfo::ADD_MOB_PACKET => new AddMobPacket(),
				ProtocolInfo::ADD_PLAYER_PACKET => new AddPlayerPacket(),
				ProtocolInfo::REMOVE_PLAYER_PACKET => new RemovePlayerPacket(),
				ProtocolInfo::ADD_ENTITY_PACKET => new AddEntityPacket(),
				ProtocolInfo::REMOVE_ENTITY_PACKET => new RemoveEntityPacket(),
				ProtocolInfo::ADD_ITEM_ENTITY_PACKET => new AddItemEntityPacket(),
				ProtocolInfo::TAKE_ITEM_ENTITY_PACKET => new TakeItemEntityPacket(),
				ProtocolInfo::MOVE_ENTITY_PACKET => new MoveEntityPacket(),
				ProtocolInfo::MOVE_ENTITY_PACKET_POSROT => new MoveEntityPacket_PosRot(),
				ProtocolInfo::ROTATE_HEAD_PACKET => new RotateHeadPacket(),
				ProtocolInfo::MOVE_PLAYER_PACKET => new MovePlayerPacket(),
				ProtocolInfo::REMOVE_BLOCK_PACKET => new RemoveBlockPacket(),
				ProtocolInfo::UPDATE_BLOCK_PACKET => new UpdateBlockPacket(),
				ProtocolInfo::ADD_PAINTING_PACKET => new AddPaintingPacket(),
				ProtocolInfo::EXPLODE_PACKET => new ExplodePacket(),
				ProtocolInfo::LEVEL_EVENT_PACKET => new LevelEventPacket(),
				ProtocolInfo::TILE_EVENT_PACKET => new TileEventPacket(),
				ProtocolInfo::ENTITY_EVENT_PACKET => new EntityEventPacket(),
				ProtocolInfo::REQUEST_CHUNK_PACKET => new RequestChunkPacket(),
				ProtocolInfo::CHUNK_DATA_PACKET => new ChunkDataPacket(),
				ProtocolInfo::PLAYER_EQUIPMENT_PACKET => new PlayerEquipmentPacket(),
				ProtocolInfo::PLAYER_ARMOR_EQUIPMENT_PACKET => new PlayerArmorEquipmentPacket(),
				ProtocolInfo::INTERACT_PACKET => new InteractPacket(),
				ProtocolInfo::USE_ITEM_PACKET => new UseItemPacket(),
				ProtocolInfo::PLAYER_ACTION_PACKET => new PlayerActionPacket(),
				ProtocolInfo::HURT_ARMOR_PACKET => new HurtArmorPacket(),
				ProtocolInfo::SET_ENTITY_DATA_PACKET => new SetEntityDataPacket(),
				ProtocolInfo::SET_ENTITY_MOTION_PACKET => new SetEntityMotionPacket(),
				ProtocolInfo::SET_HEALTH_PACKET => new SetHealthPacket(),
				ProtocolInfo::SET_SPAWN_POSITION_PACKET => new SetSpawnPositionPacket(),
				ProtocolInfo::ANIMATE_PACKET => new AnimatePacket(),
				ProtocolInfo::RESPAWN_PACKET => new RespawnPacket(),
				ProtocolInfo::SEND_INVENTORY_PACKET => new SendInventoryPacket(),
				ProtocolInfo::DROP_ITEM_PACKET => new DropItemPacket(),
				ProtocolInfo::CONTAINER_OPEN_PACKET => new ContainerOpenPacket(),
				ProtocolInfo::CONTAINER_CLOSE_PACKET => new ContainerClosePacket(),
				ProtocolInfo::CONTAINER_SET_SLOT_PACKET => new ContainerSetSlotPacket(),
				ProtocolInfo::CONTAINER_SET_DATA_PACKET => new ContainerSetDataPacket(),
				ProtocolInfo::CONTAINER_SET_CONTENT_PACKET => new ContainerSetContentPacket(),
				ProtocolInfo::CHAT_PACKET => new ChatPacket(),
				ProtocolInfo::ADVENTURE_SETTINGS_PACKET => new AdventureSettingsPacket(),
				ProtocolInfo::ENTITY_DATA_PACKET => new EntityDataPacket(),
				ProtocolInfo::SET_ENTITY_LINK_PACKET => new SetEntityLinkPacket(),
				ProtocolInfo::PLAYER_INPUT_PACKET => new PlayerInputPacket(),
				default => new UnknownPacket($pid)
			};

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