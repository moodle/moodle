# Developers

Here you will find information about our development tools and how to use them.

## .editorconfig

Please make sure your editor uses our `.editorconfig` file. It contains rules about our coding styles.

## GitHub Action Workflows

We use GitHub Actions to run our continuous integration as well as other tasks after pushing changes.
You will find related files in `.github/workflows/`.

## Development Tools and Tests

Our test related files are located in `tests` folder.
Tests are written using PHPUnit.

To install (and update) development tools like PHPUnit or PHP-CS-Fixer run:

```bash
make install-dev-tools
```

Development tools are getting installed in `dev-tools/vendor`.
Please check `dev-tools/composer.json` for more information about versions etc.
To run a tool manually, you use `dev-tools/vendor/bin`, for instance:

```bash
dev-tools/vendor/bin/php-cs-fixer fix --verbose --dry-run
```

Below are a few shortcuts to improve your developer experience.

### PHPUnit

To run all tests run:

```bash
make run-phpunit
```

### PHP-CS-Fixer

To check coding styles, run:

```bash
make run-php-cs-fixer
```

### PHPStan

To run a static code analysis, use:

```bash
make run-phpstan
```
