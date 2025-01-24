<p align="center">
    <a href="https://github.com/Terpz710/TokensAPI"><img src="https://github.com/Terpz710/TokensAPI/blob/stable/icon.png"></img></a><br>
    <b>Tokens system for Pocketmine-MP</b>

# Description

Always wanted to add a token system to your [Pocketmine-MP](https://github.com/pmmp/PocketMine-MP) server? Look no more, with this plugin you can now give players token balances! Players can pay eachother and much more!

This plugin is still under development so the plugin may seem unfinished! 🔨🧱🔧🚧🪛

Easy to use API for developers ❤️

# Commands
| Command                                   | Description                                                                   | Permission                                    | Default    |
|-------------------------------------------|-------------------------------------------------------------------------------|-----------------------------------------------|------------|
| ```/addtoken <player> <amount>```         | ```Allows the op-player to add tokens from another player token balance.```   | ```tokensapi.addtoken```                  | ```op```   |
| ```/settoken <player> <amount>```         | ```Allows the op-player to set another from anothet player token balance.```  | ```tokensapi.settoken```                  | ```op```   |
| ```/removetoken <player> <amount>```      | ```Allows the op-player to remove tokens another player token balance.```     | ```tokensapi.removetoken```               | ```op```   |
| ```/paytoken <player> <amount>```         | ```Allows the player to pay tokens to another player token balance.```        | ```tokensapi.paytoken```                  | ```true``` |
| ```/seetoken <player>```                  | ```Allows the player to see another players token balance.```                 | ```tokensapi.seetoken```                  | ```true``` |
| ```/mytoken```                            | ```Allows the player to see their token balance.```                           | ```tokensapi.mytoken```                   | ```true``` |
| ```/toptoken```                           | ```Allows the player to see the top token balances on the server.```          | ```tokensapi.topbalances```                  | ```true``` |

# Config

```
# Made by Terpz710 :p

# Starting amount
starting_token_amount: 100
```

# API💜

**How to get the token instance**
```
There are 2 ways to retrieve it:
use Terpz710\TokensAPI\Tokens;

$api = Tokens::getInstance()->getTokenAPI();

or

use Terpz710\TokensAPI\API\TokenAPI;

$api = TokenAPI::getInstance();
```

**How to retrieve a players token balance**
```
$player is an instance of Player::class

$api = TokenAPI::getInstance();

$api->getTokenBalance($player);
```

**How to add tokens to a player**
```
$player is an instance of Player::class

$amount = 100;

$api = TokenAPI::getInstance();

$api->addToken($player, $amount);

or

$api->addToken($player, 100);
```

**How to remove tokens from a player**
```
$player is an instance of Player::class

$amount = 100;

$api = TokenAPI::getInstance();

$api->removeToken($player, $amount);

or

$api->removeToken($player, 100);
```

**How to set a players token balance**
```
$player is an instance of Player::class

$amount = 100;

$api = TokenAPI::getInstance();

$api->setToken($player, $amount);

or

$api->setToken($player, 100);
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

# TODO

* Configurable messages 
* MySQL and MySQL Lite

  Got any ideas? DM me via [discord](https://discord.gg/eQpvm8Zj) Ace873056
