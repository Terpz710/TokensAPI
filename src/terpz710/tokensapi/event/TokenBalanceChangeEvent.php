<?php

declare(strict_types=1);

namespace terpz710\tokensapi\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

class TokenBalanceChangeEvent extends Event {

    public function __construct(protected Player|string $player) {

        $this->player = $player;
    }

    public function getPlayer() : Player|string{
        return $this->player;
    }
}
