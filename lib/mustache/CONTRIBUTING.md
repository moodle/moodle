# Contributions welcome!


### Here's a quick guide:

 1. [Fork the repo on GitHub](https://github.com/bobthecow/mustache.php).

 2. Update submodules: `git submodule update --init`

 3. Run the test suite. We only take pull requests with passing tests, and it's great to know that you have a clean slate. Make sure you have PHPUnit 3.5+, then run `phpunit` from the project directory.

 4. Add tests for your change. Only refactoring and documentation changes require no new tests. If you are adding functionality or fixing a bug, add a test!

 5. Make the tests pass.

 6. Push your fork to GitHub and submit a pull request against the `dev` branch.


### You can do some things to increase the chance that your pull request is accepted the first time:

 * Submit one pull request per fix or feature.
 * To help with that, do all your work in a feature branch (e.g. `feature/my-alsome-feature`).
 * Follow the conventions you see used in the project.
 * Use `phpcs --standard=PSR2` to check your changes against the coding standard.
 * Write tests that fail without your code, and pass with it.
 * Don't bump version numbers. Those will be updated — per [semver](http://semver.org) — once your change is merged into `master`.
 * Update any documentation: docblocks, README, examples, etc.
 * ... Don't update the wiki until your change is merged and released, but make a note in your pull request so we don't forget.


### Mustache.php follows the PSR-* coding standards:

 * [PSR-0: Class and file naming conventions](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 * [PSR-1: Basic coding standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
 * [PSR-2: Coding style guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
