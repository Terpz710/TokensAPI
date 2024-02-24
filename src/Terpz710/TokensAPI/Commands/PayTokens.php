<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class PayTokens extends Command {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("paytoken", "Pay tokens to another player", "/paytoken <player> <amount>");
        $this->setPermission("tokensapi.cmd.paytoken");
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
            $sender->sendMessage("Usage: /paytoken <player> <amount>");
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
        $senderTokens = $tokenAPI->getPlayerToken($sender);
        if ($senderTokens < $amount) {
            $sender->sendMessage("You don't have enough tokens to complete this transaction!");
            return false;
        }

        $tokenAPI->removeToken($sender, $amount);
        $tokenAPI->addToken($targetPlayer, $amount);

        $sender->sendMessage("You have paid $amount tokens to " . $targetPlayer->getName() . "!");
        $targetPlayer->sendMessage("You have received $amount tokens from " . $sender->getName() . "!");

        return true;
    }
}
