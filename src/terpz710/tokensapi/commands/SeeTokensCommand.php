<?php

declare(strict_types=1);

namespace terpz710\tokensapi\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat as TextColor;

use function number_format;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\utils\Error;
use terpz710\tokensapi\utils\Message;
use terpz710\tokensapi\utils\Permission;

final class SeeTokensCommand extends Command implements PluginOwned {

    private TokensAPI $plugin;

    public function __construct() {
        parent::__construct("seetokens");
        $this->setDescription("See another player's token balance");
        $this->setUsage("Usage: /seetokens <player>");
        $this->setPermission(Permission::PERM_SEETOKENS);

        $this->plugin = TokensAPI::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextColor::RED . $this->getUsage());
            return false;
        }

        $targetName = $args[0];

        if (!$this->plugin->hasTokenBalance($targetName)) {
            $sender->sendMessage((string) new Message("player-does-not-have-balance", ["{name}"], [$targetName]));
            return false;
        }

        $balance = $this->plugin->getTokenBalance($targetName);
        if ($balance === null) {
            $sender->sendMessage((string) new Message("could-not-retrieve-balance", ["{name}"], [$targetName]));
            return false;
        }

        $sender->sendMessage((string) new Message("players-balance", ["{name}", "{balance}"], [$targetName, number_format($balance)]));
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}