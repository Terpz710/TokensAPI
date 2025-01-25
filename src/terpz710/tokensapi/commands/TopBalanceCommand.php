<?php

declare(strict_types=1);

namespace terpz710\tokensapi\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat as TextColor;

use function is_numeric;
use function number_format;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\utils\Error;
use terpz710\tokensapi\utils\Message;
use terpz710\tokensapi\utils\Permission;

class TopBalanceCommand extends Command implements PluginOwned {

    private TokensAPI $plugin;

    public function __construct() {
        parent::__construct("toptokens");
        $this->setDescription("View the top players with the most tokens");
        $this->setAliases(["tokensleaderboard", "toptokenbalances"]);
        $this->setPermission(Permission::PERM_TOPBALANCES);

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

        $topBalances = TokensAPI::getInstance()->getTopTokenBalances();

        $sender->getNetworkSession()->onChatMessage("§l=== §eTop 10 Token Balances§f ===");
        $rankDisplay = 1;
        foreach ($topBalances as $data) {
            $username = $data["username"];
            $balance = is_numeric($data["balance"]) ? $data["balance"] : 0;
            $sender->getNetworkSession()->onChatMessage("§7" . $rankDisplay . ". §f" . $username . "§7 - §e" . number_format($balance) . " tokens");
            $rankDisplay++;
        }
        $sender->getNetworkSession()->onChatMessage("§l===========================");
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
}
