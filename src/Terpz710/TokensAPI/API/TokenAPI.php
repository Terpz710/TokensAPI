<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\API;

use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class TokenAPI {

    /** @var Tokens */
    private $plugin;
    
    private $playerTokens = [];

    public function __construct(Tokens $plugin) {
        $this->plugin = $plugin;
        $this->loadPlayerTokens();
    }

    private function loadPlayerTokens() {
        $dataFolder = $this->plugin->getDataFolder();
        $playerTokensFile = $dataFolder . "player_tokens.json";
        if (file_exists($playerTokensFile)) {
            $this->playerTokens = json_decode(file_get_contents($playerTokensFile), true);
        }
    }

    private function savePlayerTokens() {
        $dataFolder = $this->plugin->getDataFolder();
        $playerTokensFile = $dataFolder . "player_tokens.json";
        file_put_contents($playerTokensFile, json_encode($this->playerTokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function addToken(Player $player, int $amount) {
        $playerName = $player->getName();
        $currentTokens = $this->getPlayerToken($player);
        $newTokens = $currentTokens + $amount;
        $this->setPlayerToken($playerName, $newTokens);
    }

    public function removeToken(Player $player, int $amount) {
        $playerName = $player->getName();
        $currentTokens = $this->getPlayerToken($player);
        if ($currentTokens >= $amount) {
            $newTokens = $currentTokens - $amount;
            $this->setPlayerToken($playerName, $newTokens);
            return true;
        }
        return false;
    }

    public function setToken(Player $player, int $amount) {
        $playerName = $player->getName();
        $this->setPlayerToken($playerName, $amount);
    }

    public function getPlayerToken(Player $player): int {
        $playerName = $player->getName();
        if (isset($this->playerTokens[$playerName])) {
            return (int)$this->playerTokens[$playerName];
        }
        return 0;
    }

    private function setPlayerToken(string $playerName, int $amount) {
        $this->playerTokens[$playerName] = $amount;
        $this->savePlayerTokens();
    }
}
