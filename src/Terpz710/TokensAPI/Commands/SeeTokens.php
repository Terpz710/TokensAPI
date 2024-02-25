<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class SeeTokens extends Command {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("seetoken", "View the token balance of another player", "/seetokens <player>");
        $this->setPermission("tokensapi.cmd.seetoken");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) !== 1) {
            $sender->sendMessage("Usage: /seetokens <player>");
            return false;
        }

        $targetPlayer = $this->plugin->getServer()->getPlayerExact($args[0]);
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("Player not found!");
            return false;
        }

        $tokenAPI = $this->plugin->getTokenAPI();
        $tokens = $tokenAPI->getPlayerToken($targetPlayer);

        $sender->sendMessage("§e" . $targetPlayer->getName() . "'s token balance:§e $tokens");

        return true;
    }
}
