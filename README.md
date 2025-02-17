# This is a **PHP** framework that, unlike other ones, doesn't rely on external webservers. It creates its own based on ReactPHP, and includes a router.
- To use it:
- Run `composer install`
- Create a *wrapper* in `src` that includes the path to your autoloader (composer is prefered) and the `www` file, which is a PHP script that runs the server.
- You can configure it in `config.json`, and include the scripts you'd like to (such as route registrars) in `autorun.json` (which are located in the `Autorun` folder).
