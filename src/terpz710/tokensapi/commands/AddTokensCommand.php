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

class AddTokensCommand extends Command implements PluginOwned {

    private TokensAPI $plugin;

    public function __construct() {
        parent::__construct("addtokens");
        $this->getDescription("Add tokens to a player's balance");
        $this->setUsage("Usage: /addtokens <player> <amount>");
        $this->setPermission(Permission::PERM_ADDTOKENS);

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

        if (count($args) < 2) {
            $sender->sendMessage(TextColor::RED . $this->getUsage());
            return false;
        }

        $playerName = $args[0];
        $amount = (int) $args[1];

        if ($amount <= 0) {
            $sender->sendMessage((string) new Message("positive-amount-only"));
            return false;
        }

        if (!$this->plugin->hasTokenBalance($playerName)) {
            $sender->sendMessage((string) new Message("player-does-not-have-balance", ["{name}"], [$playerName]));
            return false;
        }

        $this->plugin->addTokens($playerName, $amount);
        $sender->sendMessage((string) new Message("successfully-added-tokens", ["{name}", "{amount}"], [$playerName, number_format($amount)]));
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}