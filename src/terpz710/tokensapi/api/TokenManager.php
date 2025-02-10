<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api;

use pocketmine\player\Player;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\event\AddTokenEvent;
use terpz710\tokensapi\event\RemoveTokenEvent;
use terpz710\tokensapi\event\SetTokenEvent;

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

    public function loadPlayerBalance(Player $player){
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();

        $this->database->executeSelect("tokens.get", ["uuid" => $uuid], function(array $rows) use ($uuid, $name) {
            $balance = !empty($rows) ? (int) $rows[0]["balance"] : 0;
            $this->tokenCache[$uuid] = ["balance" => $balance, "name" => $name];
        });
    }

    public function createTokenBalance(Player $player){
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();
        $startingAmount = $this->plugin->getConfig()->get("starting-amount");

        if ($this->hasTokenBalance($player)) {
            return;
        }

        $this->tokenCache[$uuid] = ["balance" => $startingAmount, "name" => $name];

        $this->database->executeChange("tokens.create", [
            "uuid" => $uuid,
            "name" => $name,
            "balance" => $startingAmount
        ]);
    }

    public function hasTokenBalance(Player|string $player) : bool{
        $uuid = $this->resolveUuid($player);
        return $uuid !== '' && isset($this->tokenCache[$uuid]);
    }

    public function getTokens(Player|string $player) : int{
        $uuid = $this->resolveUuid($player);
        return $uuid !== '' ? ($this->tokenCache[$uuid]['balance'] ?? 0) : 0;
    }

    public function addTokens(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getTokens($player);
        $newBalance = $oldBalance + $amount;

        (new AddTokenEvent($player, $oldBalance, $newBalance))->call();

        $this->tokenCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("tokens.add", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function removeTokens(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getTokens($player);
        $newBalance = max(0, $oldBalance - $amount);

        (new RemoveTokenEvent($player, $oldBalance, $newBalance))->call();

        $this->tokenCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("tokens.remove", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function setTokens(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getTokens($player);
        $newBalance = $amount;

        (new SetTokenEvent($player, $oldBalance, $newBalance))->call();

        $this->tokenCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("tokens.set", ["uuid" => $uuid, "amount" => $amount]);
    }

    public function getTopTokens(callable $callback){
        $this->database->executeSelect("tokens.top", [], function(array $rows) use ($callback) {
        $this->tokenCache['top'] = $rows;
        $callback($rows);
        });
    }

    private function resolveUuid(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        foreach ($this->tokenCache as $uuid => $data) {
            if (isset($data['name']) && strtolower($data['name']) === strtolower($player)) {
                return $uuid;
            }
        }

        $result = null;
        $this->database->executeSelect("tokens.get_by_name", ["name" => $player], function(array $rows) use (&$result) {
            $result = !empty($rows) ? $rows[0]["uuid"] : null;
        });

        return $result;
    }
}
