<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api;

use pocketmine\player\Player;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use terpz710\tokensapi\TokensAPI;

final class TokenManager {

    protected DataConnector $database;

    public function __construct(protected TokensAPI $plugin) {
        $this->plugin = $plugin;
    }

    public function init(): void {
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->database->executeGeneric("table.tokens");
    }

    public function createTokenBalance(Player $player): void {
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();
        $startingAmount = $this->plugin->getConfig()->get("starting-amount", 0); // Default to 0 if not set

        $this->database->executeChange("tokens.create", [
            "uuid" => $uuid,
            "name" => $name,
            "balance" => $startingAmount
        ]);
    }

    public function hasTokenBalance(Player|string $player): bool {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $result = $this->database->executeSelect("tokens.has", ["uuid" => $uuid]);
        return !empty($result);
    }

    public function getTokens(Player|string $player): int {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $result = $this->database->executeSelect("tokens.get", ["uuid" => $uuid]);
        return !empty($result) ? (int) $result[0]["balance"] : 0;
    }

    public function addTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->database->executeChange("tokens.add", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function removeTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->database->executeChange("tokens.remove", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function setTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->database->executeChange("tokens.set", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function getTopTokens(): array {
        return $this->database->executeSelect("tokens.top", []);
    }
}
