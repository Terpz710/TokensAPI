<?php

declare(strict_types=1);

namespace Terpz710\TokensAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

use Terpz710\TokensAPI\API\TokenAPI;
use Terpz710\TokensAPI\Commands\PayTokens;
use Terpz710\TokensAPI\Commands\AddTokens;
use Terpz710\TokensAPI\Commands\RemoveTokens;
use Terpz710\TokensAPI\Commands\MyTokens;
use Terpz710\TokensAPI\Commands\SeeTokens;
use Terpz710\TokensAPI\Commands\TopTokens;
use Terpz710\TokensAPI\Commands\SetTokens;

class Tokens extends PluginBase implements Listener {

    /** @var TokenAPI */
    private $tokenAPI;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->registerCommands();
        $this->tokenAPI = new TokenAPI($this);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        if (!$this->tokenAPI->getPlayerToken($player)) {
            $initialTokenAmount = $this->getConfig()->get("starting_token_amount");
            $this->tokenAPI->setToken($player, $initialTokenAmount);
        }
    }

    private function registerCommands() {
        $this->getServer()->getCommandMap()->registerAll("TokensAPI", [
            new PayTokens($this),
            new RemoveTokens($this),
	    new AddTokens($this),
	    new SeeTokens($this),
	    new MyTokens($this),
	    new TopTokens($this),
	    new SetTokens($this)
        ]);
    }

    /**
     * Get the TokenAPI instance.
     *
     * @return TokenAPI
     */
    public function getTokenAPI(): TokenAPI {
        return $this->tokenAPI;
    }
}
