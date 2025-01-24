<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api\database;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use function uasort;
use function array_slice;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\api\TokenInterface;

use terpz710\tokensapi\event\AddTokenEvent;
use terpz710\tokensapi\event\RemoveTokenEvent;
use terpz710\tokensapi\event\SetTokenEvent;

final class TokensYml implements TokenInterface {

    private Config $data;

    public function __construct() {
        $this->data = new Config(TokensAPI::getInstance()->getDataFolder() . "tokens.yml", Config::YAML, []);
    }

    private function saveData(): void {
        $this->data->save();
    }

    private function getUUID(Player|string $player) : ?string {
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        foreach ($this->data->getAll() as $uuid => $record) {
            if (strcasecmp($record["username"], $player) === 0) {
                return $uuid;
            }
        }

        return null;
    }

    public function createTokenBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            if (!$this->hasTokenBalance($player)) {
                $this->data->set($uuid, [
                    "username" => $player->getName(),
                    "balance" => TokensAPI::getInstance()->getConfig()->get("starting-amount")
                ]);
                $this->saveData();
            }
        }
    }

    public function hasTokenBalance(Player|string $player) : bool{
        $uuid = $this->getUUID($player);
        return $uuid !== null && $this->data->exists($uuid);
    }

    public function getTokenBalance(Player|string $player) : ?int {
        $uuid = $this->getUUID($player);
        return $uuid !== null ? ($this->data->get($uuid)["balance"] ?? null) : null;
    }

    public function addTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->data->get($uuid)["balance"];
        $newBalance = $oldBalance + $amount;

        $this->data->setNested("$uuid.balance", $newBalance);
        $this->saveData();

        $event = new AddTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function removeTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->data->get($uuid)["balance"];
        $newBalance = $oldBalance - $amount;

        $this->data->setNested("$uuid.balance", $newBalance);
        $this->saveData();

        $event = new RemoveTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function setTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->data->get($uuid)["balance"];
        $newBalance = $amount;

        $this->data->setNested("$uuid.balance", $newBalance);
        $this->saveData();

        $event = new SetTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function getTopTokenBalances(int $limit = 10) : array{
        $allBalances = $this->data->getAll();

        uasort($allBalances, function($a, $b) {
            return $b["balance"] <=> $a["balance"];
        });

        return array_slice($allBalances, 0, $limit, true);
    }
}