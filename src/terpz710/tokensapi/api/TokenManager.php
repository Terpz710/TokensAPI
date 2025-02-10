<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api;

use pocketmine\player\Player;

use terpz710\tokensapi\TokensAPI;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class TokenManager {
    
    protected DataConnector $database;
    
    protected array $tokenCache = [];

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

    public function loadPlayerBalance(Player $player): void {
        $uuid = $player->getUniqueId()->toString();

        $this->database->executeSelect("tokens.get", ["uuid" => $uuid], function(array $rows) use ($uuid) {
            $this->tokenCache[$uuid] = !empty($rows) ? (int) $rows[0]["balance"] : 0;
        });
    }

    public function hasTokenBalance(Player|string $player): bool {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        return isset($this->tokenCache[$uuid]);
    }

    public function getTokens(Player|string $player): int {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        return $this->tokenCache[$uuid] ?? 0;
    }

    public function addTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->tokenCache[$uuid] = ($this->tokenCache[$uuid] ?? 0) + $amount;

        $this->database->executeChange("tokens.add", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function removeTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->tokenCache[$uuid] = max(0, ($this->tokenCache[$uuid] ?? 0) - $amount);

        $this->database->executeChange("tokens.remove", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function setTokens(Player|string $player, int $amount): void {
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;
        $this->tokenCache[$uuid] = $amount;

        $this->database->executeChange("tokens.set", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function getTopTokens(): array {
        if (isset($this->tokenCache['top'])) {
            return $this->tokenCache['top'];
        }

        $this->database->executeSelect("tokens.top", [], function(array $rows) {
            $this->tokenCache['top'] = $rows;
        });

        return $this->tokenCache['top'] ?? [];
    }
}
