<?php

namespace WaterBottle;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\block\Water;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\world\sound\XpCollectSound;

class Main extends PluginBase implements Listener {

    private $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }    

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        if($item->getId() == ItemIds::BUCKET) {
            return;
        }
        if($item->getId() == ItemIds::GLASS_BOTTLE && $block->getId() == ItemIds::STILL_WATER or $block->getId() == VanillaBlocks::WATER()->getId()) {
            if($player->getInventory()->canAddItem(ItemFactory::getInstance()->get(ItemIds::POTION, 0, 1))) {
                $item = ItemFactory::getInstance()->get(ItemIds::POTION, 0, 1);
                $item->setCustomName($this->config->getNested("items.potion_name"));
                $player->getInventory()->addItem($item);
                $level = $player->getWorld();
                $getx = round($player->getPosition()->getX());
                $gety = round($player->getPosition()->getY());
                $getz = round($player->getPosition()->getZ());
                $vect = new Vector3($getx, $gety, $getz);
                $player->sendPopup($this->config->getNested("messages.filled_bottle"));
                $player->getWorld()->addSound($player->getPosition(), new XpCollectSound(), [$player]);
                
                $player->getInventory()->removeItem(ItemFactory::getInstance()->get(ItemIds::GLASS_BOTTLE, 0, 1));
            } else {
                $player->sendMessage($this->config->getNested("messages.full_inventory"));
            }
        }        

    }
}
