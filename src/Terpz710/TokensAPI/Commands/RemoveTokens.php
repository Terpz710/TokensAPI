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
        parent::__construct("removetokens", "Remove tokens from a player's balance", "/removetokens <player> <amount>");
        $this->setPermission("tokensapi.cmd.removetokens");
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
            $sender->sendMessage("Please enter a valid amount greater than 0!");
            return false;
        }

        $tokenAPI = $this->plugin->getTokenAPI();
        $targetTokens = $tokenAPI->getPlayerToken($targetPlayer);
        if ($targetTokens < $amount) {
            $sender->sendMessage("The target player doesn't have enough tokens!");
            return false;
        }

        $tokenAPI->removeToken($targetPlayer, $amount);

        $sender->sendMessage("$amount tokens have been removed from " . $targetPlayer->getName() . "'s balance!");

        return true;
    }
}
