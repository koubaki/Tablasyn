# This is a **PHP** framework that, unlike other ones, doesn't rely on external webservers. It creates its own based on ReactPHP, and includes a router.
- To use it:
- Run `composer install`
- Create a *wrapper* **outside** `src` that includes the path to your autoloader (Composer is prefered) and the `www.php` file, which is a script that runs the app.
- You can configure it in `config.json`, and include the scripts you'd like to (such as route registrars) in `autorun.json` (which are located in the `Autorun` folder).
- If port `9500` isn't available, open `config.json` and set `port` to `3000` or another number.
