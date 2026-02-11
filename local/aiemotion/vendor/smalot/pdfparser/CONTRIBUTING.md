# Contributing

**Please read the following text before creating a pull request.**

This project is organized and supported by contributions from the community. Maintenance is done in our limited time.
We welcome any pull request that contributes to PDFParser (code, documentation, ...).
However, we would like to point out that you are initially responsible for a contribution.
If you are new to dealing with pull requests, you can find more information at [Github documentation](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests).
Please don't just throw code at us and expect us to handle it.
Nevertheless, we will support you and give you feedback.

To make life easier for you and us, there is a Continuous Integration (CI) system that carries out software tests and performs a number of other tasks.
The following points describe the relevant preparations/inputs for the CI system.
All checks must be green, otherwise a pull request will not be accepted.
* Please create an [issue](https://github.com/smalot/pdfparser/issues) before starting work on any significant changes.
* We only accept code that is bundled with tests, regardless of whether it is a new function or a bug fix. This strengthens the code base and avoids later regressions. :exclamation: **If you don't know how to write a test, tell us upfront when you open the pull request and we might add them ourselves or discuss other ways**. This [Medium article](https://pguso.medium.com/a-beginners-guide-to-phpunit-writing-and-running-unit-tests-in-php-d0b23b96749f) might be a good starting point. Code changes without tests are very likely to be rejected.
* Fix reported issues with the coding style. We use **PHP-CS-Fixer** for this. See [.php-cs-fixer.php](./.php-cs-fixer.php) for more information about our coding styles. [Developer.md](./doc/Developer.md) contains more information about this topic.
* If you are fixing an **existing error**, refer to it in the introduction text of the pull request. For example, if you created a fix for issue `#1234` write the following Markdown: `fixes #1234`.
* In case you have changed **internal behavior/functionality**, check our documentation to make sure these changes are **correctly documented**: https://github.com/smalot/pdfparser/tree/master/doc
