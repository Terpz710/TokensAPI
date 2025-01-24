<?php

declare(strict_types=1);

namespace terpz710\tokensapi\event;

use pocketmine\player\Player;

class SetTokenEvent extends TokenEvent {

    public function __construct(Player|string $player, int $oldBalance, int $newBalance) {
        parent::__construct($player, $oldBalance, $newBalance);
    }

    public function getChangeType() : string{
        return "set";
    }
}