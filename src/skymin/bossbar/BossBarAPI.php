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
	/** @var string[] */
	private array $players = [];
	
	private bool $running = false;
	
	private function isData(Player $player) :bool{
		return (isset($this->players[strtolower($player->getName())]));
	}
	
	public function run() :void{
		if(!$this->running){
			$this->id = Entity::nextRuntimeId();
			$this->running = true;
		}
	}
	
	public function sendBossBar(Player $player, string $title = '', float $percent = 100.0) :void{
		if(!$this->running){
			throw new \LogicException('is not running!');
			return;
		}
		$this->removeBossBar($player);
		$network = $player->getNetworkSession();
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
		$this->players[] = strtolower($player->getName());
		$pk = AddActorPacket::create(
			EntityLegacyIds::SLIME,
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
		$network->sendDataPacket(BossEventPacket::show($this->id, $title, $percent));
	}
	
	public function removeBossBar(Player $player) :void{
		if(!$this->isData($player)) return;
		unset($this->players[strtolower($player->getName())]);
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
