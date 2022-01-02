<?php
/**
 *      _                    _       
 *  ___| | ___   _ _ __ ___ (_)_ __  
 * / __| |/ / | | | '_ ` _ \| | '_ \ 
 * \__ \   <| |_| | | | | | | | | | |
 * |___/_|\_\\__, |_| |_| |_|_|_| |_|
 *           |___/ 
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 * 
 * @author skymin
 * @link   https://github.com/sky-min
 * @license https://opensource.org/licenses/MIT MIT License
 * 
 *   /\___/\
 * 　(∩`・ω・)
 * ＿/_ミつ/￣￣￣/
 * 　　＼/＿＿＿/
 *
 */
 
declare(strict_types = 1);

namespace skymin\bossbar;

use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use pocketmine\entity\{Entity, Attribute as Att};

use pocketmine\network\mcpe\protocol\{AddActorPacket, BossEventPacket};
use pocketmine\network\mcpe\protocol\types\entity\{
	EntityIds,
	Attribute,
	EntityMetadataCollection,
	EntityMetadataProperties,
	EntityMetadataFlags
};

final class BossBarAPI{
	use SingletonTrait;
	
	private array $id = [];
	
	private array $players = [];
	
	public const COLOR_PINK = 0;
	public const COLOR_BLUE = 1;
	public const COLOR_RED = 2;
	public const COLOR_GREEN = 3 ;
	public const COLOR_YELLOW = 4;
	public const COLOR_PURPLE = 5;
	public const COLOR_WHITE = 6;
	
	private function isData(Player $player, int $channel) :bool{
		return (isset($this->players[$player->getId()][$channel]));
	}
	
	public function sendBossBar(Player $player, string $title = '', int $channel = 0, float $percent = 1.0, int $color = self::COLOR_PURPLE) :void{
		$this->hideBossBar($player, $channel);
		$network = $player->getNetworkSession();
		if(!$this->isData($player, $channel)){
			$metadata = new EntityMetadataCollection();
			$metadata->setGenericFlag(EntityMetadataFlags::FIRE_IMMUNE, true);
			$metadata->setGenericFlag(EntityMetadataFlags::SILENT, true);
			$metadata->setGenericFlag(EntityMetadataFlags::INVISIBLE, true);
			$metadata->setGenericFlag(EntityMetadataFlags::NO_AI, true);
			$metadata->setString(EntityMetadataProperties::NAMETAG, '');
			$metadata->setFloat(EntityMetadataProperties::SCALE, 0.0);
			$metadata->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, -1);
			$metadata->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0.0);
			$metadata->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0.0);
			$this->players[$player->getId()][$channel - 1] = true;
			$pk = AddActorPacket::create(
				isset($this->id[$channel]) ? $this->id[$channel] : $this->id[$channel] = Entity::nextRuntimeId(),
				$this->id[$channel],
				EntityIds::SLIME,
				$player->getPosition(),
				null,
				0.0,
				0.0,
				0.0,
				[new Attribute(Att::HEALTH, 0.0, 100.0, 100.0, 100.0)],
				$metadata->getAll(),
				[]
			);
			$network->sendDataPacket($pk);
		}
		$pk = BossEventPacket::show($this->id[$channel], $title, $percent);
		$pk->color = $color;
		$network->sendDataPacket($pk);
	}
	
	public function hideBossBar(Player $player, int $channel = 0) :void{
		if($this->isData($player, $channel)){
			$player->getNetworkSession()->sendDataPacket(BossEventPacket::hide($this->id[$channel]));
		}
	}
	
	public function setTitle(Player $player, string $title, int $channel = 0) :void{
		if($this->isData($player, $channel)){
			$player->getNetworkSession()->sendDataPacket(BossEventPacket::title($this->id[$channel], $title));
		}
	}
	
	public function setPercent(Player $player, float $percent, int $channel = 0) :void{
		if($this->isData($player, $channel)){
			$player->getNetworkSession()->sendDataPacket(BossEventPacket::healthPercent($this->id[$channel], $percent));
		}
	}
	
	public function deleteData(Player $player) : void{
		if(isset($this->players[$player->getId()])){
			unset($this->players[$player->getId()]);
		}
	}
	
}