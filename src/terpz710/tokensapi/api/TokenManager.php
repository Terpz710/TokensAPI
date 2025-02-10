<?php

declare(strict_types=1);

namespace wavycraft\tokens;

use pocketmine\player\Player;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

use Closure;

final class TokenManager {

    protected DataConnector $database;

    public function __construct(protected TokensAPI $plugin) {
        $this->plugin = $plugin;
    }

    public function init() : void{
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);
        $this->database->executeGeneric("table.hub");
    }

    public function createTokenBalance(Player|string $player): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeChange("tokens.create", ["name" => $name]);
    }

    public function hasTokenBalance(Player|string $player, Closure $callback): void {
        $name = $player instanceof Player ? $player->getName() : $player;
        $this->database->executeSelect("tokens.has", ["name" => $name], function (array $rows) use ($callback) {
            $callback(!empty($rows));
        });
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

    public function getTopTokens(Closure $callback): void {
        $this->database->executeSelect("tokens.top", [], function (array $rows) use ($callback) {
            $callback($rows);
        });
    }
}
