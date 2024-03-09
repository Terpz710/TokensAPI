<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\player\Player;

use Terpz710\TokensAPI\Tokens;

class MyTokens extends Command implements PluginOwned {

    /** @var Tokens */
    private $plugin;

    public function __construct(Tokens $plugin) {
        parent::__construct("mytoken", "View your current token balance", "/mytokens");
        $this->setPermission("tokensapi.cmd.mytoken");
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

        $tokenAPI = $this->plugin->getTokenAPI();
        $tokens = $tokenAPI->getPlayerToken($sender);

        $sender->sendMessage("Your token balance: §e{$tokens}");

        return true;
    }
}
