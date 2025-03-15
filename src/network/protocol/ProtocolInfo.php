<?php


abstract class ProtocolInfo{

	const CURRENT_PROTOCOL = 14;

	const PING_PACKET = 0x00;

	const PONG_PACKET = 0x03;

	const CLIENT_CONNECT_PACKET = 0x09;
	const SERVER_HANDSHAKE_PACKET = 0x10;

	const CLIENT_HANDSHAKE_PACKET = 0x13;
	//const SERVER_FULL_PACKET = 0x14;
	const DISCONNECT_PACKET = 0x15;
	const LOGIN_PACKET = 0x82;
	const LOGIN_STATUS_PACKET = 0x83;
	const READY_PACKET = 0x84;
	const MESSAGE_PACKET = 0x85;
	const SET_TIME_PACKET = 0x86;
	const START_GAME_PACKET = 0x87;
	const ADD_MOB_PACKET = 0x88;
	const ADD_PLAYER_PACKET = 0x89;
	const REMOVE_PLAYER_PACKET = 0x8a;

	const ADD_ENTITY_PACKET = 0x8c;
	const REMOVE_ENTITY_PACKET = 0x8d;
	const ADD_ITEM_ENTITY_PACKET = 0x8e;
	const TAKE_ITEM_ENTITY_PACKET = 0x8f;
	const MOVE_ENTITY_PACKET = 0x90;

	const MOVE_ENTITY_PACKET_POSROT = 0x93;
	const ROTATE_HEAD_PACKET = 0x94;
	const MOVE_PLAYER_PACKET = 0x95;
	//const PLACE_BLOCK_PACKET = 0x96;
	const REMOVE_BLOCK_PACKET = 0x97;
	const UPDATE_BLOCK_PACKET = 0x98;
	const ADD_PAINTING_PACKET = 0x99;
	const EXPLODE_PACKET = 0x9a;
	const LEVEL_EVENT_PACKET = 0x9b;
	const TILE_EVENT_PACKET = 0x9c;
	const ENTITY_EVENT_PACKET = 0x9d;
	const REQUEST_CHUNK_PACKET = 0x9e;
	const CHUNK_DATA_PACKET = 0x9f;
	const PLAYER_EQUIPMENT_PACKET = 0xa0;
	const PLAYER_ARMOR_EQUIPMENT_PACKET = 0xa1;
	const INTERACT_PACKET = 0xa2;
	const USE_ITEM_PACKET = 0xa3;
	const PLAYER_ACTION_PACKET = 0xa4;

	const HURT_ARMOR_PACKET = 0xa6;
	const SET_ENTITY_DATA_PACKET = 0xa7;
	const SET_ENTITY_MOTION_PACKET = 0xa8;
	const SET_ENTITY_LINK_PACKET = 0xa9;
	const SET_HEALTH_PACKET = 0xaa;
	const SET_SPAWN_POSITION_PACKET = 0xab;
	const ANIMATE_PACKET = 0xac;
	const RESPAWN_PACKET = 0xad;
	const SEND_INVENTORY_PACKET = 0xae;
	const DROP_ITEM_PACKET = 0xaf;
	const CONTAINER_OPEN_PACKET = 0xb0;
	const CONTAINER_CLOSE_PACKET = 0xb1;
	const CONTAINER_SET_SLOT_PACKET = 0xb2;
	const CONTAINER_SET_DATA_PACKET = 0xb3;
	const CONTAINER_SET_CONTENT_PACKET = 0xb4;
	//const CONTAINER_ACK_PACKET = 0xb5;
	const CHAT_PACKET = 0xb6;
	const ADVENTURE_SETTINGS_PACKET = 0xb7;
	const ENTITY_DATA_PACKET = 0xb8;
	const PLAYER_INPUT_PACKET = 0xb9;

}
/*Unused:
 * 0xb5
 * 0x96
 * 0x17
 * 0x14
 */
abstract class ProtocolInfo12{

    const CURRENT_PROTOCOL_12 = 12;

    const PING_PACKET = 0x00;

    const PONG_PACKET = 0x03;

    const CLIENT_CONNECT_PACKET = 0x09;
    const SERVER_HANDSHAKE_PACKET = 0x10;

    const CLIENT_HANDSHAKE_PACKET = 0x13;
    //const SERVER_FULL_PACKET = 0x14;
    const DISCONNECT_PACKET = 0x15;
    const LOGIN_PACKET = 0x82;
    const LOGIN_STATUS_PACKET = 0x83;
    const READY_PACKET = 0x84;
    const MESSAGE_PACKET = 0x85;
    const SET_TIME_PACKET = 0x86;
    const START_GAME_PACKET = 0x87;
    const ADD_MOB_PACKET = 0x88;
    const ADD_PLAYER_PACKET = 0x89;
    const REMOVE_PLAYER_PACKET = 0x8a;

    const ADD_ENTITY_PACKET = 0x8c;
    const REMOVE_ENTITY_PACKET = 0x8d;
    const ADD_ITEM_ENTITY_PACKET = 0x8e;
    const TAKE_ITEM_ENTITY_PACKET = 0x8f;
    const MOVE_ENTITY_PACKET = 0x90;

    const MOVE_ENTITY_PACKET_POSROT = 0x93;
    const ROTATE_HEAD_PACKET = 0xff;
    const MOVE_PLAYER_PACKET = 0x94;
    //const PLACE_BLOCK_PACKET = 0x96;
    const REMOVE_BLOCK_PACKET = 0x96;
    const UPDATE_BLOCK_PACKET = 0x97;
    const ADD_PAINTING_PACKET = 0x98;
    const EXPLODE_PACKET = 0x99;
    const LEVEL_EVENT_PACKET = 0x9a;
    const TILE_EVENT_PACKET = 0x9b;
    const ENTITY_EVENT_PACKET = 0x9c;
    const REQUEST_CHUNK_PACKET = 0x9d;
    const CHUNK_DATA_PACKET = 0x9e;
    const PLAYER_EQUIPMENT_PACKET = 0x9f;
    const PLAYER_ARMOR_EQUIPMENT_PACKET = 0xa0;
    const INTERACT_PACKET = 0xa1;
    const USE_ITEM_PACKET = 0xa2;
    const PLAYER_ACTION_PACKET = 0xa3;

    const HURT_ARMOR_PACKET = 0xa5;
    const SET_ENTITY_DATA_PACKET = 0xa6;
    const SET_ENTITY_MOTION_PACKET = 0xa7;
    const SET_ENTITY_LINK_PACKET = 0xa8;
    const SET_HEALTH_PACKET = 0xa9;
    const SET_SPAWN_POSITION_PACKET = 0xaa;
    const ANIMATE_PACKET = 0xab;
    const RESPAWN_PACKET = 0xac;
    const SEND_INVENTORY_PACKET = 0xad;
    const DROP_ITEM_PACKET = 0xae;
    const CONTAINER_OPEN_PACKET = 0xaf;
    const CONTAINER_CLOSE_PACKET = 0xb0;
    const CONTAINER_SET_SLOT_PACKET = 0xb1;
    const CONTAINER_SET_DATA_PACKET = 0xb2;
    const CONTAINER_SET_CONTENT_PACKET = 0xb3;
    //const CONTAINER_ACK_PACKET = 0xb5;  //Bruh
    const CHAT_PACKET = 0xb5;
    const ADVENTURE_SETTINGS_PACKET = 0xb6;
    const ENTITY_DATA_PACKET = 0xb7;
    const PLAYER_INPUT_PACKET = 0xb9;
}
abstract class ProtocolInfo9{

    const CURRENT_PROTOCOL_9 = 9;

    const PING_PACKET = 0x00;

    const PONG_PACKET = 0x03;

    const CLIENT_CONNECT_PACKET = 0x09;
    const SERVER_HANDSHAKE_PACKET = 0x10;

    const CLIENT_HANDSHAKE_PACKET = 0x13;
    //const SERVER_FULL_PACKET = 0x14;
    const DISCONNECT_PACKET = 0x15;
    const LOGIN_PACKET = 0x82;
    const LOGIN_STATUS_PACKET = 0x83;
    const READY_PACKET = 0x84;
    const MESSAGE_PACKET = 0x85;
    const SET_TIME_PACKET = 0x86;
    const START_GAME_PACKET = 0x87;
    const ADD_MOB_PACKET = 0x88;
    const ADD_PLAYER_PACKET = 0x89;
    const REMOVE_PLAYER_PACKET = 0x8a;

    const ADD_ENTITY_PACKET = 0x8c;
    const REMOVE_ENTITY_PACKET = 0x8d;
    const ADD_ITEM_ENTITY_PACKET = 0x8e;
    const TAKE_ITEM_ENTITY_PACKET = 0x8f;
    const MOVE_ENTITY_PACKET = 0x90;

    const MOVE_ENTITY_PACKET_POSROT = 0x93;
    const MOVE_PLAYER_PACKET = 0x94;
    //const PLACE_BLOCK_PACKET = 0x95;
    const REMOVE_BLOCK_PACKET = 0x96;
    const UPDATE_BLOCK_PACKET = 0x97;
    const ADD_PAINTING_PACKET = 0x98;
    const EXPLODE_PACKET = 0x99;
    const LEVEL_EVENT_PACKET = 0x9a;
    const TILE_EVENT_PACKET = 0x9b;
    const ENTITY_EVENT_PACKET = 0x9c;
    const REQUEST_CHUNK_PACKET = 0x9d;
    const CHUNK_DATA_PACKET = 0x9e;
    const PLAYER_EQUIPMENT_PACKET = 0x9f;
    const PLAYER_ARMOR_EQUIPMENT_PACKET = 0xa0;
    const INTERACT_PACKET = 0xa1;
    const USE_ITEM_PACKET = 0xa2;
    const PLAYER_ACTION_PACKET = 0xa3;

    const HURT_ARMOR_PACKET = 0xa5;
    const SET_ENTITY_DATA_PACKET = 0xa6;
    const SET_ENTITY_MOTION_PACKET = 0xa7;
    //const SET_ENTITY_LINK_PACKET = 0xa?;// Change
    const SET_HEALTH_PACKET = 0xa8;// Change
    const SET_SPAWN_POSITION_PACKET = 0xa9;
    const ANIMATE_PACKET = 0xaa;
    const RESPAWN_PACKET = 0xab;
    const SEND_INVENTORY_PACKET = 0xac;
    const DROP_ITEM_PACKET = 0xad;
    const CONTAINER_OPEN_PACKET = 0xae;
    const CONTAINER_CLOSE_PACKET = 0xaf;
    const CONTAINER_SET_SLOT_PACKET = 0xb0;
    const CONTAINER_SET_DATA_PACKET = 0xb1;
    const CONTAINER_SET_CONTENT_PACKET = 0xb2;
    //const CONTAINER_ACK_PACKET = 0xb3;
    const CHAT_PACKET = 0xb4;//12 change
    const ADVENTURE_SETTINGS_PACKET = 0xb6;
    const ENTITY_DATA_PACKET = 0xb7;
    const PLAYER_INPUT_PACKET = 0xb9;

}
abstract class ProtocolInfo7{

    const CURRENT_PROTOCOL_7 = 7;

    const PING_PACKET = 0x00;

    const PONG_PACKET = 0x03;

    const CLIENT_CONNECT_PACKET = 0x09;
    const SERVER_HANDSHAKE_PACKET = 0x10;

    const CLIENT_HANDSHAKE_PACKET = 0x13;
    //const SERVER_FULL_PACKET = 0x14;
    const DISCONNECT_PACKET = 0x15;
    const LOGIN_PACKET = 0x82;
    const LOGIN_STATUS_PACKET = 0x83;
    const READY_PACKET = 0x84;
    const MESSAGE_PACKET = 0x85;
    const SET_TIME_PACKET = 0x86;
    const START_GAME_PACKET = 0x87;
    const ADD_MOB_PACKET = 0x88;
    const ADD_PLAYER_PACKET = 0x89;
    const REMOVE_PLAYER_PACKET = 0x8a;//Maybe Exist

    const ADD_ENTITY_PACKET = 0x8c;
    const REMOVE_ENTITY_PACKET = 0x8d;
    const ADD_ITEM_ENTITY_PACKET = 0x8e;
    const TAKE_ITEM_ENTITY_PACKET = 0x8f;
    const MOVE_ENTITY_PACKET = 0x90;

    const MOVE_ENTITY_PACKET_POSROT = 0x93;
    const MOVE_PLAYER_PACKET = 0x94;
    //const PLACE_BLOCK_PACKET = 0x95;
    const REMOVE_BLOCK_PACKET = 0x96;
    const UPDATE_BLOCK_PACKET = 0x97;
    const ADD_PAINTING_PACKET = 0x98;// Maybe exist
    const EXPLODE_PACKET = 0x99;
    const LEVEL_EVENT_PACKET = 0x9a;
    const TILE_EVENT_PACKET = 0x9b;//Maybe exist
    const ENTITY_EVENT_PACKET = 0x9c;
    const REQUEST_CHUNK_PACKET = 0x9d;
    const CHUNK_DATA_PACKET = 0x9e;
    const PLAYER_EQUIPMENT_PACKET = 0x9f;
    //const PLAYER_ARMOR_EQUIPMENT_PACKET = 0xa0;
    const INTERACT_PACKET = 0xa0;
    const USE_ITEM_PACKET = 0xa1;
    const PLAYER_ACTION_PACKET = 0xa2;

    //const HURT_ARMOR_PACKET = 0xa3;
    const SET_ENTITY_DATA_PACKET = 0xa3;
    const SET_ENTITY_MOTION_PACKET = 0xa4;
    //const SET_ENTITY_LINK_PACKET = 0xa?;// Change
    const SET_HEALTH_PACKET = 0xa5;// Change
    const SET_SPAWN_POSITION_PACKET = 0xa6;
    const ANIMATE_PACKET = 0xa7;
    const RESPAWN_PACKET = 0xa8;
    const SEND_INVENTORY_PACKET = 0xa9;//Maybe exist
    const DROP_ITEM_PACKET = 0xaa;
    const CONTAINER_OPEN_PACKET = 0xab;
    const CONTAINER_CLOSE_PACKET = 0xac;
    const CONTAINER_SET_SLOT_PACKET = 0xad;
    //const CONTAINER_SET_DATA_PACKET = 0xb1;
    //const CONTAINER_SET_CONTENT_PACKET = 0xb2;
    //const CONTAINER_ACK_PACKET = 0xb3;
    const CHAT_PACKET = 0xb1;//12 change
    const ADVENTURE_SETTINGS_PACKET = 0xb3;
    const ENTITY_DATA_PACKET = 0xb2;
    //const PLAYER_INPUT_PACKET = 0xb9;
}
/***REM_START***/
require_once(FILE_PATH . "src/network/raknet/RakNetDataPacket.php");
/***REM_END***/