<?php

declare(strict_types=1);

namespace terpz710\tokensapi;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use pocketmine\player\Player;

use function mkdir;
use function is_dir;
use function file_exists;

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

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        $languageFolder = $this->getDataFolder() . "languages/";
        if (!is_dir($languageFolder)) {
            mkdir($languageFolder, 0777, true);
        }

        $langConfig = [
            "english_messages.yml", 
            "spanish_messages.yml", 
            "german_messages.yml", 
            "traditional_chinese_messages.yml", 
            "french_messages.yml"
        ];
        
        foreach ($langConfig as $file) {
            if (!file_exists($languageFolder . $file)) {
                $this->saveResource("languages/" . $file, false);
            }
        }

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

        $this->manager = new TokenManager($this);
        $this->manager->init();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    protected function getTokenManager() : TokenManager{
        return $this->manager;
    }

    public function createTokenBalance(Player $player){
        return $this->getTokenManager()->createTokenBalance($player);
    }

    public function hasTokenBalance(Player|string $player) : bool{
        return $this->getTokenManager()->hasTokenBalance($player);
    }

    public function getTokens(Player|string $player) : ?int{
        return $this->getTokenManager()->getTokens($player);
    }

    public function addTokens(Player|string $player, int $amount){
        return $this->getTokenManager()->addTokens($player, $amount);
    }

    public function removeTokens(Player|string $player, int $amount){
        return $this->getTokenManager()->removeTokens($player, $amount);
    }

    public function setTokens(Player|string $player, int $amount){
        return $this->getTokenManager()->setTokens($player, $amount);
    }

    public function getTopTokens() : array{
        return $this->getTokenManager()->getTopTokens();
    }
}
