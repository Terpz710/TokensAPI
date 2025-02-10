<?php

declare(strict_types=1);

namespace terpz710\tokensapi;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $manager = TokensAPI::getInstance();

        if (!$manager->hasTokenBalance($player)) {
            $manager->createTokenBalance($player);
        }

        $manager->loadPlayerBalance($player);
    }
}
