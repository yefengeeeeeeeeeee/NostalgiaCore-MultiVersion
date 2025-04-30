<?php

class PacketPool{

	public const ACCEPTED_PROTOCOLS = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

	public static bool $isInit = false;
	/** @var RakNetDataPacket[][] */
	private static array $packetPool = [];

	/**
	 * @param class-string<RakNetDataPacket> $class
	 */
	private static function registerPacket(string $class, $protocols = self::ACCEPTED_PROTOCOLS): void{
		/** @var RakNetDataPacket $pk */
		$pk = new $class;
		foreach ($protocols as $protocol) {
			$pk->PROTOCOL = $protocol;
			$pid = $pk->pid();
			if(isset(self::$packetPool[$protocol][$pid])) {
				$oldPk = self::$packetPool[$protocol][$pid];
				console("[WARNING] 协议$protocol 中 PID:$pid (0x".dechex($pid).")包".get_class($oldPk)."与包".get_class($pk)."冲突");
			}
			self::$packetPool[$protocol][$pid] = clone $pk;
		}
	}

	public static function getPacket($pid, $protocol) : RakNetDataPacket{
		if(isset(self::$packetPool[$protocol][$pid])) {
			return clone self::$packetPool[$protocol][$pid];
		}
		$pk = new UnknownPacket();
		$pk->packetID = $pid;
		$pk->PROTOCOL = $protocol;
		return $pk;
	}

    public static function isPacketExist($pid, $protocol) : bool{
        if(isset(self::$packetPool[$protocol][$pid])) {
            return true;
        }
        return false;
    }

	public static function init() : void{
		if(self::$isInit){
			return;
		}

		self::registerPacket(AddEntityPacket::class);
		self::registerPacket(AddItemEntityPacket::class);
		self::registerPacket(AddMobPacket::class);
		self::registerPacket(AddPaintingPacket::class, [7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(AddPlayerPacket::class);
		self::registerPacket(AdventureSettingsPacket::class, [7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(AnimatePacket::class);

		self::registerPacket(ChatPacket::class, [5, 6, 7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(ChunkDataPacket::class);
		self::registerPacket(ClientConnectPacket::class);
		self::registerPacket(ClientHandshakePacket::class);
		self::registerPacket(ContainerClosePacket::class);
		self::registerPacket(ContainerOpenPacket::class);
		self::registerPacket(ContainerSetContentPacket::class);
		self::registerPacket(ContainerSetDataPacket::class);
		self::registerPacket(ContainerSetSlotPacket::class);
		self::registerPacket(ContainerAckPacket::class);

		self::registerPacket(DisconnectPacket::class);
		self::registerPacket(DropItemPacket::class);

		self::registerPacket(EntityDataPacket::class, [7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(EntityEventPacket::class);
		self::registerPacket(ExplodePacket::class);

		self::registerPacket(HurtArmorPacket::class, [9, 10, 11, 12, 13, 14]);

		self::registerPacket(InteractPacket::class);

		self::registerPacket(LevelEventPacket::class);
		self::registerPacket(LoginPacket::class);
		self::registerPacket(LoginStatusPacket::class);

		self::registerPacket(MessagePacket::class);
		self::registerPacket(MoveEntityPacket::class);
		self::registerPacket(MoveEntityPacket_PosRot::class);
		self::registerPacket(MovePlayerPacket::class);

		self::registerPacket(PingPacket::class);
		self::registerPacket(PlaceBlockPacket::class);
		self::registerPacket(PlayerActionPacket::class, [5, 6, 7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(PlayerArmorEquipmentPacket::class, [9, 10, 11, 12, 13, 14]);
		self::registerPacket(PlayerEquipmentPacket::class);
		self::registerPacket(PlayerInputPacket::class, [7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(PongPacket::class);

		self::registerPacket(ReadyPacket::class);
		self::registerPacket(RemoveBlockPacket::class);
		self::registerPacket(RemoveEntityPacket::class);
		self::registerPacket(RemovePlayerPacket::class);
		self::registerPacket(RequestChunkPacket::class);
		self::registerPacket(RespawnPacket::class);
		self::registerPacket(RotateHeadPacket::class);

		self::registerPacket(SendInventoryPacket::class);
		self::registerPacket(ServerHandshakePacket::class);
		self::registerPacket(SetEntityDataPacket::class);
		self::registerPacket(SetEntityLinkPacket::class, [12, 13, 14]);
		self::registerPacket(SetEntityMotionPacket::class, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(SetHealthPacket::class);
		self::registerPacket(SetSpawnPositionPacket::class, [7, 8, 9, 10, 11, 12, 13, 14]);
		self::registerPacket(SetTimePacket::class);
		self::registerPacket(StartGamePacket::class);

		self::registerPacket(TakeItemEntityPacket::class);
		self::registerPacket(TileEventPacket::class, [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]);

		self::registerPacket(UpdateBlockPacket::class);
		self::registerPacket(UseItemPacket::class);

		//self::registerPacket(UnknownPacket::class); //DO NOT REGISTER THIS

		self::$isInit = true;
	}
}