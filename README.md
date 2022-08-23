# bossbarapi
![Screenshot_20220101-234521_Minecraft](https://user-images.githubusercontent.com/81374952/147854399-6c663dfe-786e-4298-902d-5b72edf066f9.jpg)

pmmp 4.0 virion

# how to use
## Send BossBar
```php
BossBarAPI::getInstance()->sendBossBar(Player $player, string $title, int $channel, float $percent, int $color);
//$channel is 0~
```
color list
```php
BossBarAPI::COLOR_PINK
BossBarAPI::COLOR_BLUE
BossBarAPI::COLOR_RED
BossBarAPI::COLOR_GREEN
BossBarAPI::COLOR_YELLOW
BossBarAPI::COLOR_PURPLE
BossBarAPI::COLOR_WHITE
```
## Change Title
**you have to send BossBar already.**
```php
BossBarAPI::getInstance()->setTitle(Player $player, string $title, int $channel);
```
## setPercent
**you have to send BossBar already.**
```php
BossBarAPI::getInstance()->setPercent(Player $player, float $percent, int $channel);
```
## hideBossBar
**you have to send BossBar already.**
```php
BossBarAPI::getInstance()->hideBossBar(Player $player, int $channel = 0);
```
## others
**If you want to automatically delete player channel information when player left**
```php
BossBarHandler::autoDeleteData(Plugin $plugin);
```
**Please be careful**
 [issue](https://github.com/sky-min/bossbarapi/issues/5)

If the world is unloaded, please delete the player channel information and send bossbar again
```php
BossBarAPI::getInstance()->deleteData(Player $player);
BossBarAPI::getInstance()->sendBossBar(...)
```
