<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api\database;

use SQLite3;

use pocketmine\player\Player;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\api\TokenInterface;

use terpz710\tokensapi\event\AddTokenEvent;
use terpz710\tokensapi\event\RemoveTokenEvent;
use terpz710\tokensapi\event\SetTokenEvent;

final class TokensSQLite implements TokenInterface {

    private SQLite3 $database;

    public function __construct() {
        $dataFolder = TokensAPI::getInstance()->getDataFolder();
        $this->database = new SQLite3($dataFolder . "tokens.db");

        $this->database->exec("
            CREATE TABLE IF NOT EXISTS tokens (
                uuid TEXT PRIMARY KEY,
                username TEXT,
                balance INTEGER DEFAULT 0
            );
        ");
    }

    public function hasTokenBalance(Player|string $player) : bool{
        $query = "SELECT 1 FROM tokens WHERE uuid = :uuid OR LOWER(username) = LOWER(:username);";
        $statement = $this->database->prepare($query);

        if ($player instanceof Player) {
            $statement->bindValue(":uuid", $player->getUniqueId()->toString(), SQLITE3_TEXT);
            $statement->bindValue(":username", $player->getName(), SQLITE3_TEXT);
        } else {
            $statement->bindValue(":uuid", "", SQLITE3_TEXT);
            $statement->bindValue(":username", $player, SQLITE3_TEXT);
        }

        $result = $statement->execute();
        $exists = $result->fetchArray(SQLITE3_ASSOC) !== false;
        $statement->close();

        return $exists;
    }

    public function createTokenBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();

            if (!$this->hasTokenBalance($player)) {
                $statement = $this->database->prepare("
                    INSERT INTO tokens (uuid, username, balance) 
                    VALUES (:uuid, :username, :balance);
                ");
                $statement->bindValue(":uuid", $uuid, SQLITE3_TEXT);
                $statement->bindValue(":username", $username, SQLITE3_TEXT);
                $statement->bindValue(":balance", TokensAPI::getInstance()->getConfig()->get("starting-amount"), SQLITE3_INTEGER);
                $statement->execute();
                $statement->close();
            }
        }
    }

    public function getTokenBalance(Player|string $player) : ?int{
        $query = "SELECT balance FROM tokens WHERE uuid = :uuid OR LOWER(username) = LOWER(:username);";
        $statement = $this->database->prepare($query);

        if ($player instanceof Player) {
            $statement->bindValue(":uuid", $player->getUniqueId()->toString(), SQLITE3_TEXT);
            $statement->bindValue(":username", $player->getName(), SQLITE3_TEXT);
        } else {
            $statement->bindValue(":uuid", "", SQLITE3_TEXT);
            $statement->bindValue(":username", $player, SQLITE3_TEXT);
        }

        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
        $statement->close();

        return $data ? (int)$data["balance"] : null;
    }

    public function addTokens(Player|string $player, int $amount) : void{
        $oldBalance = $this->getTokenBalance($player) ?? 0;
        $newBalance = $oldBalance + $amount;

        $this->updateTokenBalance($player, $newBalance);

        $event = new AddTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function removeTokens(Player|string $player, int $amount) : void{
        $oldBalance = $this->getTokenBalance($player) ?? 0;
        $newBalance = max(0, $oldBalance - $amount);

        $this->updateTokenBalance($player, $newBalance);

        $event = new RemoveTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function setTokens(Player|string $player, int $amount) : void{
        $oldBalance = $this->getTokenBalance($player) ?? 0;
        $newBalance = $amount;

        $this->updateTokenBalance($player, $newBalance);

        $event = new SetTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function getTopTokenBalances(int $limit = 10) : array{
        $query = "SELECT username, balance FROM tokens ORDER BY balance DESC LIMIT :limit;";
        $statement = $this->database->prepare($query);
        $statement->bindValue(":limit", $limit, SQLITE3_INTEGER);

        $result = $statement->execute();
        $topBalances = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $topBalances[] = [
                "username" => $row["username"],
                "balance" => (int)$row["balance"]
            ];
        }

        $statement->close();
        return $topBalances;
    }

    private function updateTokenBalance(Player|string $player, int $newBalance) : void{
        $uuid = null;
        $username = null;

        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();
        } else {
            $username = $player;
            $uuid = $this->getUUIDByUsername($username);
        }

        if ($uuid !== null) {
            $statement = $this->database->prepare("
                UPDATE tokens SET balance = :balance WHERE uuid = :uuid;
            ");
            $statement->bindValue(":balance", $newBalance, SQLITE3_INTEGER);
            $statement->bindValue(":uuid", $uuid, SQLITE3_TEXT);
            $statement->execute();
            $statement->close();
        }
    }

    private function getUUIDByUsername(string $username) : ?string{
        $statement = $this->database->prepare("SELECT uuid FROM tokens WHERE LOWER(username) = LOWER(:username);");
        $statement->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
        $statement->close();

        return $data["uuid"] ?? null;
    }
}