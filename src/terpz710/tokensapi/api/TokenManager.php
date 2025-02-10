<?php

declare(strict_types=1);

namespace wavycraft\tokens;

use pocketmine\player\Player;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class TokenManager {

    private DataConnector $database;

    public function __construct(private TokensAPI $plugin) {
        $this->init();
    }

    private function init(): void {
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->database->executeGeneric("tokens.init");
    }

    public function createTokenBalance(Player|string $player): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeChange("tokens.create", ["name" => $name]);
    }

    public function hasTokenBalance(Player|string $player): bool {
        $name = $player instanceof Player ? $player->getName() : $player;
        $result = $this->database->executeSelect("tokens.has", ["name" => $name]);
        return !empty($result);
    }

    public function getTokens(Player|string $player): int {
        $name = $player instanceof Player ? $player->getName() : $player;
        $result = $this->database->executeSelect("tokens.get", ["name" => $name]);
        return !empty($result) ? (int) $result[0]["balance"] : 0;
    }

    public function addTokens(Player|string $player, int $amount): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeChange("tokens.add", ["name" => $name, "amount" => $amount]);
    }

    public function removeTokens(Player|string $player, int $amount): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeChange("tokens.remove", ["name" => $name, "amount" => $amount]);
    }

    public function setTokens(Player|string $player, int $amount): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeChange("tokens.set", ["name" => $name, "amount" => $amount]);
    }

    public function getTopTokens(): array {
        return $this->database->executeSelect("tokens.top", []);
    }
}
