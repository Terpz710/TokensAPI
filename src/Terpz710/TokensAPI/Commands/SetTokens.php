<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class SetTokens extends Command implements PluginOwned {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("settoken", "Set a player's token balance to a specific amount", "/settoken <player> <amount>");
        $this->setPermission("tokensapi.cmd.settoken");
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
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
            $sender->sendMessage("Usage: /settoken <player> <amount>");
            return false;
        }

        $targetPlayer = $this->plugin->getServer()->getPlayerExact($args[0]);
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("Player not found!");
            return false;
        }

        $amount = (int) $args[1];
        if ($amount < 0) {
            $sender->sendMessage("Please enter a valid amount greater than or equal to §c0§f!");
            return false;
        }

        $tokenAPI = $this->plugin->getTokenAPI();
        $tokenAPI->setToken($targetPlayer, $amount);

        $sender->sendMessage("Set §e" . $targetPlayer->getName() . "'s token §fbalance to §e{$amount}!");

        return true;
    }
}
