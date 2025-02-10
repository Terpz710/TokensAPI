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

    public function init(){
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->database->executeGeneric("table.tokens");
    }

    public function createTokenBalance(Player $player){
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();
        $startingAmount = $this->plugin->getConfig()->get("starting-amount", 0);

        $this->database->executeChange("tokens.create", [
            "uuid" => $uuid,
            "name" => $name,
            "balance" => $startingAmount
        ], function() use ($uuid, $startingAmount) {
            $this->tokenCache[$uuid] = $startingAmount;
        });
    }

    public function hasTokenBalance(Player|string $player) : bool{
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;

        if (isset($this->tokenCache[$uuid])) {
            return true;
        }

        $this->database->executeSelect("tokens.has", ["uuid" => $uuid], function(array $rows) use ($uuid) {
            $this->tokenCache[$uuid] = !empty($rows);
        });

        return isset($this->tokenCache[$uuid]) ? $this->tokenCache[$uuid] : false;
    }

    public function getTokens(Player|string $player) : int{
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;

        if (isset($this->tokenCache[$uuid])) {
            return $this->tokenCache[$uuid];
        }

        $this->database->executeSelect("tokens.get", ["uuid" => $uuid], function(array $rows) use ($uuid) {
            $this->tokenCache[$uuid] = !empty($rows) ? (int) $rows[0]["balance"] : 0;
        });

        return $this->tokenCache[$uuid] ?? 0;
    }

    public function addTokens(Player|string $player, int $amount){
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;

        $this->database->executeChange("tokens.add", ["uuid" => $uuid, "amount" => $amount], function() use ($uuid, $amount) {
            $this->tokenCache[$uuid] = ($this->tokenCache[$uuid] ?? 0) + $amount;
        });
    }

    public function removeTokens(Player|string $player, int $amount){
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;

        $this->database->executeChange("tokens.remove", ["uuid" => $uuid, "amount" => $amount], function() use ($uuid, $amount) {
            $this->tokenCache[$uuid] = max(0, ($this->tokenCache[$uuid] ?? 0) - $amount);
        });
    }

    public function setTokens(Player|string $player, int $amount){
        $uuid = $player instanceof Player ? $player->getUniqueId()->toString() : $player;

        $this->database->executeChange("tokens.set", ["uuid" => $uuid, "amount" => $amount], function() use ($uuid, $amount) {
            $this->tokenCache[$uuid] = $amount;
        });
    }

    public function getTopTokens() : array{
        if (isset($this->tokenCache['top'])) {
            return $this->tokenCache['top'];
        }

        $this->database->executeSelect("tokens.top", [], function(array $rows) {
            $this->tokenCache['top'] = $rows;
        });

        return $this->tokenCache['top'] ?? [];
    }
}
