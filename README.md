<p align="center">
    <a href="https://github.com/Terpz710/TokensAPI"><img src="https://github.com/Terpz710/TokensAPI/blob/stable/icon.png"></img></a><br>
    <b>Tokens system for Pocketmine-MP</b>

# Description

Always wanted to add a token system to your [Pocketmine-MP](https://github.com/pmmp/PocketMine-MP) server? Look no more, with this plugin you can now give players token balances! Players can pay eachother and much more!

This plugin also supports multi-language support, The only downside is that I used Google translate so some messages may appear out of placed/unright. Sorry if Google butchered your language! Help is always wanted so if you want to contribute please open a pull request thank you!

This plugin is still under development so the plugin may seem unfinished! 🔨🧱🔧🚧🪛

Easy to use API for developers! ❤️

**TokensAPI** had a huge code rewrite on January 24, 2025

***This rewrite introduced new features aswell as fixes/improvements. Configurable messages, added multiple storage types for storing token balances and rewrote 99% of the code.***

# Features
* Configurable messages
* Multi-language support (English, Spanish, German, French, Traditional Chinese, Simplified Chinese and more to come!)
* 2 storage types (MySQL and SQLite)
* Easy to use API
* ScoreHud support (See `Addon` section)

# Commands
| Command                                   | Description                                                                   | Permission                                    | Default    |
|-------------------------------------------|-------------------------------------------------------------------------------|-----------------------------------------------|------------|
| ```/addtokens <player> <amount>```         | ```Allows the op-player to add tokens from another player token balance.```   | ```tokensapi.addtoken```                  | ```op```   |
| ```/settokens <player> <amount>```         | ```Allows the op-player to set another from anothet player token balance.```  | ```tokensapi.settoken```                  | ```op```   |
| ```/removetokens <player> <amount>```      | ```Allows the op-player to remove tokens another player token balance.```     | ```tokensapi.removetoken```               | ```op```   |
| ```/paytokens <player> <amount>```         | ```Allows the player to pay tokens to another player token balance.```        | ```tokensapi.paytoken```                  | ```true``` |
| ```/seetokens <player>```                  | ```Allows the player to see another players token balance.```                 | ```tokensapi.seetoken```                  | ```true``` |
| ```/mytokens```                            | ```Allows the player to see their token balance.```                           | ```tokensapi.mytoken```                   | ```true``` |
| ```/toptokens```                           | ```Allows the player to see the top token balances on the server.```          | ```tokensapi.topbalances```                  | ```true``` |

# Config

```php
# Terpz710

# NOTE: All translations were done via Google translate, Sorry if Google butchered your language!
# Choose the language for all the messages:
# Options:
# - "en": English (default)
# - "es": Spanish
# - "fr": French
# - "de": German
# - "zh-t": Traditional Chinese
# - "zh-s": Simplified Chinese
language: "en"

# Choose the storage type for player token balances:
database:
  # The database type. "sqlite" and "mysql" are supported.
  type: sqlite

  # Edit these settings only if you choose "sqlite".
  sqlite:
    # The file name of the database in the plugin data folder.
    # You can also put an absolute path here.
    file: tokens.db

  # Edit these settings only if you choose "mysql".
  mysql:
    host: 127.0.0.1
    # Avoid using the "root" user for security reasons.
    username: root
    password: ""
    schema: your_schema

  # The maximum number of simultaneous SQL queries
  # Recommended: 1 for sqlite, 2 for MySQL. You may want to further increase this value if your MySQL connection is very slow.
  worker-limit: 1

# Starting amount for new player accounts
starting-amount: 1000
```

# API for Developers ❤️

**How to get the token instance:**
```php
/** Import this class */
use terpz710\tokensapi\TokensAPI;

$api = TokensAPI::getInstance();
```

**How to retrieve a players token balance:**
```php
/** $player can be either an instance of Player::class or PlayerName (e.g. Steve) */

$api = TokensAPI::getInstance();

$api->getTokens($player);

or

$name = "Steve";

$api->getTokens($name);
```

**How to add tokens to a player:**
```php
/** $player can be either an instance of Player::class or PlayerName (e.g. Steve) */

$amount = 100;

$api = TokensAPI::getInstance();

$api->addTokens($player, $amount);

or

$name = "Steve";

$amount = 100;

$api->addTokens($name, $amount);
```

**How to remove tokens from a player:**
```php
/** $player can be either an instance of Player::class or PlayerName (e.g. Steve) */

$amount = 100;

$api = TokensAPI::getInstance();

$api->removeTokens($player, $amount);

or

$name = "Steve";

$amount = 100;

$api->removeTokens($name, $amount);
```

**How to set a players token balance:**
```php
/** $player can be either an instance of Player::class or PlayerName (e.g. Steve) */

$amount = 100;

$api = TokensAPI::getInstance();

$api->setTokens($player, $amount);

or

$name = "Steve";

$amount = 100;

$api->setTokens($name, $amount);
```

**How to check if a player has a token balance before adding tokens:**
```php
/** $player can be either an instance of Player::class or PlayerName (e.g. Steve) */

$amount = 100;

$api = TokensAPI::getInstance();

/**
 * Call this before attempting to add/remove/set tokens
 * Take a look at AddTokensCommand.php for a refrence
 */
if (!$api->hasTokenBalance($player)) {
    $player->sendMessage("Steve does not have a token balance");
    return;
}

$api->setTokens($player, $amount);

or

$name = "Steve";

$amount = 100;

/**
 * Call this before attempting to add/remove/set tokens
 * Take a look at AddTokensCommand.php for a refrence
 */
if (!$api->hasTokenBalance($name)) {
    $player->sendMessage($name . " does not have a token balance");
    return;
}

$api->setTokens($name, $amount);
```

# Open a pull request

Make sure to make a fork before creating a pull request.

Note I dont accept any pull request!

I wont accept request IF the following change isnt major.

Click below to open a pull request:

[Click me](https://github.com/Terpz710/TokensAPI/pulls)

# Open a bug report here

I appreciate your help and support to make sure this project is bug/error free!

Click below to sumbit a bug report:

[Click me](https://github.com/Terpz710/TokensAPI/issues/new)

# Addon

This plugin supports ScoreHud!

Link to addon: [TokensAPIScoreTag](https://github.com/Terpz710/TokensAPIScoreTag/tree/main)

# TODO

- [x] Configurable messages (completed)
- [x] MySQL and MySQL Lite (completed)
- [x] Multi-language support (completed)

  Got any ideas? DM me via [discord](https://discord.gg/eQpvm8Zj) Ace873056
