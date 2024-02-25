<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use Terpz710\TokensAPI\Tokens;

class TopTokens extends Command {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("toptokens", "Display the top players with the highest token balances", "/toptokens");
        $this->setPermission("tokensapi.cmd.toptoken");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        $tokenAPI = $this->plugin->getTokenAPI();
        $topPlayers = $this->getTopPlayers($this->plugin);

        $sender->sendMessage("--- Top Players with Highest Token Balances ---");
        $rank = 1;
        foreach ($topPlayers as $playerName => $tokens) {
            $sender->sendMessage("§7{$rank}. §f{$playerName}: §e{$tokens} tokens");
            $rank++;
        }

        return true;
    }

    private function getTopPlayers(Tokens $plugin): array {
        $playerTokens = [];
        $onlinePlayers = $plugin->getServer()->getOnlinePlayers();
        $tokenAPI = $plugin->getTokenAPI();
        foreach ($onlinePlayers as $player) {
            $playerTokens[$player->getName()] = $tokenAPI->getPlayerToken($player);
        }
        arsort($playerTokens);
        return array_slice($playerTokens, 0, 10, true);
    }
}
