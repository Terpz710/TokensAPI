<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api\database;

use mysqli;

use pocketmine\player\Player;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\api\TokenInterface;

use terpz710\tokensapi\event\AddTokenEvent;
use terpz710\tokensapi\event\RemoveTokenEvent;
use terpz710\tokensapi\event\SetTokenEvent;

final class TokensMySQL implements TokenInterface {

    private function getConnection() : mysqli{
        $config = TokensAPI::getInstance()->getConfig();
        $host = $config->get("mysql-host");
        $user = $config->get("mysql-user");
        $password = $config->get("mysql-password");
        $database = $config->get("mysql-database");

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            throw new \RuntimeException("Failed to connect to MySQL: " . $connection->connect_error);
        }

        return $connection;
    }

    private function getUUID(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT uuid FROM tokens WHERE username = ?");
        $stmt->bind_param("s", $player);
        $stmt->execute();
        $result = $stmt->get_result();
        $uuid = $result->fetch_assoc()["uuid"] ?? null;
        $stmt->close();
        $connection->close();

        return $uuid;
    }

    public function createTokenBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();
        } else {
            return;
        }

        if (!$this->hasTokenBalance($player)) {
            $connection = $this->getConnection();
            $startingAmount = TokensAPI::getInstance()->getConfig()->get("starting-amount");
            $stmt = $connection->prepare("INSERT INTO tokens (uuid, username, balance) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $uuid, $username, $startingAmount);
            $stmt->execute();
            $stmt->close();
            $connection->close();
        }
    }

    public function hasTokenBalance(Player|string $player) : bool{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return false;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT 1 FROM tokens WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        $connection->close();

        return $exists;
    }

    public function getTokenBalance(Player|string $player) : ?int{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return null;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT balance FROM tokens WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $balance = $result->fetch_assoc()["balance"] ?? null;
        $stmt->close();
        $connection->close();

        return $balance !== null ? (int)$balance : null;
    }

    public function addTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->getTokenBalance($player) ?? 0;
        $newBalance = $oldBalance + $amount;

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE tokens SET balance = ? WHERE uuid = ?");
        $stmt->bind_param("is", $newBalance, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        $event = new AddTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function removeTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->getTokenBalance($player) ?? 0;
        $newBalance = max(0, $oldBalance - $amount);

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE tokens SET balance = ? WHERE uuid = ?");
        $stmt->bind_param("is", $newBalance, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        $event = new RemoveTokenEvent($player, $oldBalance, $newBalance);
        $event->call();
    }

    public function setTokens(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $oldBalance = $this->getTokenBalance($player) ?? 0;

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE tokens SET balance = ? WHERE uuid = ?");
        $stmt->bind_param("is", $amount, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        $event = new SetTokenEvent($player, $oldBalance, $amount);
        $event->call();
    }

    public function getTopTokenBalances(int $limit = 10) : array{
        $connection = $this->getConnection();

        $stmt = $connection->prepare("SELECT username, balance FROM tokens ORDER BY balance DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $topBalances = [];
        while ($row = $result->fetch_assoc()) {
            $topBalances[] = [
                "username" => $row["username"],
                "balance" => (int)$row["balance"]
            ];
        }

        $stmt->close();
        $connection->close();

        return $topBalances;
    }
}