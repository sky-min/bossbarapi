<?php
declare(strict_types = 1);

namespace skymin\bossbar;

use pocketmine\utils\SingletonTrait;

use pocketmine\player\Player;

use pocketmine\entity\{Entity, Attribute as Att};
use pocketmine\data\bedrock\EntityLegacyIds;

use pocketmine\network\mcpe\protocol\{AddActorPacket, BossEventPacket};
use pocketmine\network\mcpe\protocol\types\entity\{EntityIds, Attribute, EntityMetadataCollection, EntityMetadataProperties, EntityMetadataFlags};

use function strtolower;

final class BossBarAPI{
	use SingletonTrait;
	
	private ?int $id = null;
	
	private array $players = [];
	
	public const COLOR_PINK = 0;
	public const COLOR_BLUE = 1;
	public const COLOR_RED = 2;
	public const COLOR_GREEN = 3 ;
	public const COLOR_YELLOW = 4;
	public const COLOR_PURPLE = 5;
	public const COLOR_WHITE = 6;
	
	private function isData(Player $player) :bool{
		return (isset($this->players[$player->getId()]));
	}
	
	public function sendBossBar(Player $player, string $title = '', float $percent = 1.0, int $color = self::COLOR_PURPLE) :void{
		$this->hideBossBar($player);
		$network = $player->getNetworkSession();
		if(!$this->isData($player)){
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
			$this->players[$player->getId()] = true;
			$pk = AddActorPacket::create(
				$this->id ?? $this->id = Entity::nextRuntimeId(),
				$this->id,
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
		$pk = BossEventPacket::show($this->id, $title, $percent);
		$pk->color = $color;
		$network->sendDataPacket($pk);
	}
	
	public function hideBossBar(Player $player) :void{
		if(!$this->isData($player)) return;
		$player->getNetworkSession()->sendDataPacket(BossEventPacket::hide($this->id));
	}
	
	public function setTitle(Player $player, string $title) :void{
		if(!$this->isData($player)) return;
		$player->getNetworkSession()->sendDataPacket(BossEventPacket::title($this->id, $title));
	}
	
	public function setPercent(Player $player, float $percent) :void{
		if(!$this->isData($player)) return;
		$player->getNetworkSession()->sendDataPacket(BossEventPacket::healthPercent($this->id, $percent));
	}
	
}
