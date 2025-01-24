<?php

declare(strict_types=1);

namespace terpz710\tokensapi;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use pocketmine\player\Player;

use terpz710\tokensapi\api\TokenManager;

use terpz710\tokensapi\commands\AddTokensCommand;
use terpz710\tokensapi\commands\RemoveTokensCommand;
use terpz710\tokensapi\commands\SetTokensCommand;
use terpz710\tokensapi\commands\PayTokensCommand;
use terpz710\tokensapi\commands\MyTokensCommand;
use terpz710\tokensapi\commands\SeeTokensCommand;
use terpz710\tokensapi\commands\TopBalanceCommand;

final class TokensAPI extends PluginBase {

    protected static self $instance;

    protected TokenManager $manager;

    public Config $messages;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        $this->saveResource("messages.yml");

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getCommandMap()->registerAll("TokensAPI", [
            new AddTokensCommand(),
            new RemoveTokensCommand(),
            new SetTokensCommand(),
            new PayTokensCommand(),
            new MyTokensCommand(),
            new SeeTokensCommand(),
            new TopBalanceCommand()
        ]);

        $this->messages = new Config($this->getDataFolder() . "messages.yml");

        $this->manager = new TokenManager();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    private function getTokenHandler() {
        return $this->manager->getHandler();
    }

    public function createTokenBalance(Player|string $player) {
        return $this->getTokenHandler()->createTokenBalance($player);
    }

    public function hasTokenBalance(Player|string $player) : bool{
        return $this->getTokenHandler()->hasTokenBalance($player);
    }

    public function getTokenBalance(Player|string $player) : ?int{
        return $this->getTokenHandler()->getTokenBalance($player);
    }

    public function addTokens(Player|string $player, int $amount) {
        return $this->getTokenHandler()->addTokens($player, $amount);
    }

    public function removeTokens(Player|string $player, int $amount) {
        return $this->getTokenHandler()->removeTokens($player, $amount);
    }

    public function setTokens(Player|string $player, int $amount) {
        return $this->getTokenHandler()->setTokens($player, $amount);
    }

    public function getTopTokenBalances(int $limit = 10) : array{
        return $this->getTokenHandler()->getTopTokenBalances($limit);
    }
}