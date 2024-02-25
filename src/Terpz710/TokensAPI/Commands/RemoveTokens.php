<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class RemoveTokens extends Command {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("removetoken", "Remove tokens from a player's balance", "/removetokens <player> <amount>");
        $this->setPermission("tokensapi.cmd.removetoken");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game!");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) !== 2) {
            $sender->sendMessage("Usage: /removetokens <player> <amount>");
            return false;
        }

        $targetPlayer = $this->plugin->getServer()->getPlayerExact($args[0]);
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("Player not found!");
            return false;
        }

        $amount = (int) $args[1];
        if ($amount <= 0) {
            $sender->sendMessage("Please enter a valid amount greater than §c0§f!");
            return false;
        }

        $tokenAPI = $this->plugin->getTokenAPI();
        $targetTokens = $tokenAPI->getPlayerToken($targetPlayer);
        if ($targetTokens < $amount) {
            $sender->sendMessage("§e" . $targetPlayer->getName() . "doesn't have enough §etokens§f!");
            return false;
        }

        $tokenAPI->removeToken($targetPlayer, $amount);

        $sender->sendMessage("§e{$amount}§f tokens have been removed from§e " . $targetPlayer->getName() . "'s §fbalance!");

        return true;
    }
}
