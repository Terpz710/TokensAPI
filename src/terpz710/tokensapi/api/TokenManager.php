<?php

declare(strict_types=1);

namespace terpz710\tokensapi\api;

use terpz710\tokensapi\TokensAPI;

use terpz710\tokensapi\api\database\TokensJson;
use terpz710\tokensapi\api\database\TokensMySQL;
use terpz710\tokensapi\api\database\TokensSQLite;
use terpz710\tokensapi\api\database\TokensTxt;
use terpz710\tokensapi\api\database\TokensYml;

final class TokenManager {

    private TokenInterface $handler;

    public function __construct() {
        $databaseType = TokensAPI::getInstance()->getConfig()->get("storage");

        if ($databaseType === "json") {
            $this->handler = new TokensJson();
        } elseif ($databaseType === "mysql") {
            $this->handler = new TokensMySQL();
        } elseif ($databaseType === "sqlite") {
            $this->handler = new TokensSQLite();
        } elseif ($databaseType === "txt") {
            $this->handler = new TokensTxt();
        } elseif ($databaseType === "yml") {
            $this->handler = new TokensYml();
        } else {
            throw new \InvalidArgumentException("Invalid storage type specified in the configuration: " . $databaseType);
        }
    }

    public function getHandler() : TokenInterface{
        return $this->handler;
    }
}