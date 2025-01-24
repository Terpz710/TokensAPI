<?php

declare(strict_types=1);

namespace terpz710\tokensapi\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

abstract class TokenEvent extends Event {

    private Player|string $player;
    private int $oldBalance;
    private int $newBalance;

    public function __construct(Player|string $player, int $oldBalance, int $newBalance) {
        $this->player = $player;
        $this->oldBalance = $oldBalance;
        $this->newBalance = $newBalance;
    }

    public function getPlayer() : Player|string{
        return $this->player;
    }

    public function getOldBalance() : int{
        return $this->oldBalance;
    }

    public function getNewBalance() : int{
        return $this->newBalance;
    }

    abstract public function getChangeType(): string;

}