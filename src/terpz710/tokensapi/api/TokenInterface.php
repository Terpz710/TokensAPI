<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api;

use pocketmine\player\Player;

interface TokenInterface {

    public function createTokenBalance(Player|string $player) : void;

    public function hasTokenBalance(Player|string $player) : bool;

    public function getTokenBalance(Player|string $player) : ?int;

    public function addTokens(Player|string $player, int $amount) : void;

    public function removeTokens(Player|string $player, int $amount) : void;

    public function setTokens(Player|string $player, int $amount) : void;

    public function getTopTokenBalances(int $limit = 10) : array;

}