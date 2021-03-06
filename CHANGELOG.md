# Changelog

## Unknown version

### Breaking changes

- `b5bef60` Added the `TELEGRAM_WEBHOOK_ROUTE` key to the `.env.example` file. This value will define the route used for the webhook. Read the example envfile file for details. After changing it, it is recommended to regenerate the webhook (`php artisan telegram:webhook --setup --all`).  
- `b5bef60` The `TELEGRAM_WEBHOOK_URL` key on the `.env` file should reflect the route used by the `TELEGRAM_WEBHOOK_ROUTE` env value.
- `b5bef60` Deleted the `BOFHERS_TELEGRAM_WEBHOOK_KEY` key. `TELEGRAM_WEBHOOK_ROUTE` is the substitute for webhook obfuscation.

Update instructions:

1. Replace all the `TELEGRAM_*` variables in the `.env` file with those from the `.env.example` file. 
2. Fill them accordingly to whatever the needs are.
3. Regenerate the webhook endpoint: `php artisan telegram:webhook --setup --all`.
4. Register the bot's commands: `php artisan telegram:registerBotCommands`.

### New features

- `d6f272e` Adapted all the `!commands` so that they use Telegram's command's API which uses `/` instead of `!` (fixes #32). Added deprecation notices to old `!commands`.
- `b5bef60` Added the artisan command `telegram:registerBotCommands` to integrate the bot into Telegram's commands API (see #32).
- `b5bef60` Modified the docker testing environment and documentation to auto register webhook and bot commands (see #32).
- `92c4e4a` `/quote` now accepts an optional argument _category_ which allows the bot to only show a given category's quotes (`/quote mycategory`).
- `92c4e4a` `/addquote` now allows to add a category to the new quote. The format `/addquote <text> %% <category>` should be used when trying to categorize a new quote. (fixes #10 via categorizing quotes with `random_insult`, also see #5 as you could categorize quotes with `congrats`). 
- `a742350` New command `/categorias`. As of now it only works for categories of quotes.

### Fixes and refactors 

- `b5bef60` Removed the `/set-webhook` and `/del-webhook` routes. Added the artisan command `telegram:webhook` to manage the webhook (fixes #13).
- `b5bef60` Removed the `/random` route.
- `b5bef60` Refactored a few instances of code so that the Telegram service is instantiated via dependency injection instead of using it manually (this is required so that it reads the full configuration on the `config/telegram.php` file).
- `b5bef60` Tweaked a few values on the `config/telegram.php` file to enable commands and proper webhook registration.
- `de10710` As of now, composer v2 is not playing nicely with backpack. Rolling back to v1.
- `2ee0614` Preventing an error on the webhook's handling method from causing the bot to become unresponsive.  

## 0.0.2

- `21ba7b9` Adds a version command to the bot

## 0.0.1

- `bb206fc` Merge pull request #26 from hluaces/fix-issue-24
- `41a8fc2` fix: !addquote wont accept 0 and some other garbage
- `fa16b54` Send hourly quotes only to BOFHers, people in other channels is complaining. Fixes #23
- `4881e3d` Merge branch 'master' of https://github.com/oscarmlage/bofhers
- `e6a6abc` Added a mark in backend to see the already used quotes
- `41ad58f` Fixes #22
- `7b32a14` Migrations change
- `1131a06` Feature, now Tifu replies to the mesage and adds a photo in the message
- `2b7b016` Merge pull request #21 from pniaps/master
- `c4d07b2` Merge branch 'master' into master
- `97061ce` Added a minor comment
- `c77194e` Feature, a kind of "ruskifilter" :). Fixes #6. Idea and code: @wakkah
- `e25e6b3` Random debug
- `53e29bb` Feature, quote rotation. Fixes #17 (code by @wakkah)
- `dab8f6d` Adds scheduled quotes to TifuBot (fixes #11, fixes #3)
- `353774d` Removes debug log files (fixes #18)
- `a4b2214` Fixes relation between quotes and categories
- `b3649e4` Rima de covid sólo cuando el mensaje termina en covid, no distingue mayúsculas
- `ee94feb` Changed order quotes
- `50dcf5b` Added ability to change quote status selected quotes in a bunch
- `fa63503` Added repo command to @TifuBot
- `4c89309` Adds covid feature (fixes #15)
- `4054f83` Fixes #14
- `a4d9e04` Added a kind of protection with TELEGRAM_WEBHOOK_KEY in the environment for the public urls (til they're not converted to cli commands)
- `3ad11d6` Working on tifu commands
- `ca0fed4` Added quotes + categories for jobs and quotes + some other minor crud improvements.
- `ef1456d` Updated composer vulnerabilities
- `b5fcd54` Remove bot token from VerifyCsrfToken
- `eb69308` Working on tifu commands + Suggestions in TODO
- `5c0c5c8` New db migration + working on tifu commands
- `bdab86a` Working in tifu commands
- `443a69f` Minor improvements in Cruds
- `7457992` Deleted leftover in migrations
- `10f467f` Added Telegram Channels support
- `954b273` Improving Telegram capturer
- `856fcb7` Rollback
- `5d43948` Remove bot key from VerifyCsrfToken
- `eeba501` Change TifuBot commands
- `ef8ad98` Mixed commit with multiple stuff
- `794bd6b` Update TODO cosmetics
- `f1e1191` Add BOFHers as User extension + UsersCategories
- `1ff8546` Add categories
- `47d7d28` Update goals
- `8e20414` Update gitignore
- `862bd5f` Fix gitignore
- `c2fc973` Add software + jobs feature
- `9499586` Add vanilla Laravel
- `262ddc6` Initial commit
