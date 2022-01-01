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

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerQuitEvent;

final class BossBarHandler{
	
	private static $delete = false;
	
	public static function autoDeleteData(Plugin $plugin) :void{
		if(self::$delete) return;
		Server::getInstance()->getPluginManager()->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $ev) :void{
			BossBarAPI::getInstance()->deleteData($ev->getPlayer());
		}, EventPriority::MONITOR, $plugin);
	}
	
}