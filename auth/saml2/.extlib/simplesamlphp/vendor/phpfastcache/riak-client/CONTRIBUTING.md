##Contributing

This repo's maintainers are engineers at Basho and we welcome your contribution to the project!

### An honest disclaimer

Due to our obsession with stability and our rich ecosystem of users, community updates on this repo may take a little longer to review. 

The most helpful way to contribute is by reporting your experience through issues. Issues may not be updated while we review internally, but they're still incredibly appreciated.

Thank you for being part of the community! We love you for it. 

## Helping through sample code

The most immediately helpful way you can benefit this project is by forking the repo, **adding further [examples](/examples)** and submitting a pull request.

## How-to contribute to the PHP client

**_IMPORTANT_**: This is an open source project licensed under the Apache 2.0 License. We encourage contributions to the project from the community. We ask that you keep in mind these considerations when planning your contribution.

* Whether your contribution is for a bug fix or a feature request, **create an [Issue](https://github.com/basho/riak-php-client/issues)** and let us know what you are thinking.
* **For bugs**, if you have already found a fix, feel free to submit a Pull Request referencing the Issue you created.
* **For feature requests**, we want to improve upon the library incrementally which means small changes at a time. In order ensure your PR can be reviewed in a timely manner, please keep PRs small, e.g. <10 files and <500 lines changed. If you think this is unrealistic, then mention that within the Issue and we can discuss it.
* Before you open the PR, please review the following regarding Coding Standards, Docblock comments and unit / integration tests to reduce delays in getting your changes approved.

### Pull Request Process

Here's how to get started:

* Fork the appropriate sub-projects that are affected by your change. 
* Create a topic branch for your change and checkout that branch.
     `git checkout -b some-topic-branch`
* Make your changes and run the test suite if one is provided. (see below)
* Commit your changes and push them to your fork.
* Open pull requests for the appropriate projects.
* Contributors will review your pull request, suggest changes, and merge it when it's ready and/or offer feedback.

### Coding Standards
Here are the standards we expect to see when considering pull requests

* [PSR-2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PHP / Pear Docblock Guide](http://pear.php.net/manual/en/standards.sample.php)
* [PHPUnit Tests](https://phpunit.de/manual/current/en/phpunit-book.html)
* Please suffix all Interfaces and Traits with the descriptors Interface and Trait, e.g. `ObjectInterface` & `ObjectTrait`

### Docblock Comments
It is expected that any new code is updated with a reasonable docblock comment. 

This includes:

* Short & long descriptions (where appropriate) for all classes and all class members, properties and constants.
* Please add code examples where it makes sense using the `<code>` tag.
* A `@author` tag need to be included on all new classes.
* A `@var` tag on every class property
* A `@param` tag for every parameter on a class method
* A `@return` tag for every class method that returns a value
* A `@throws` tag for every method that has a thrown execution within it 

Here is an example of a class docblock:
```php
/**
 * A more elaborate description of what this class does. It may include warnings, limitations or examples.
 *
 * <code>
 * $nodes = (new Node\Builder)
 *   ->atHost('localhost')
 *   ->onPort(8098)
 *   ->build()
 *
 * $riak = new Riak($nodes);
 * </code>
 *
 * @author  Author Name <author@domain.com>
 */
```

It is not necessary to add short / long descriptions to simple getters & setters. Use the `@since` tag where appropriate.

### Tests
We want to ensure that all code that is included in a release has proper coverage with unit tests. It is expected that
all pull requests that include new classes or class methods have appropriate unit tests included with the PR.

There are three types of tests that we use, each has been setup within the phpunit.xml within the root of the library and are described below.
<dl>
<dt>Unit Tests</dt>
<dd>Focus is on each unit of work. It is the most verbose as each member of every class should be tested. These tests use mock objects, so they do not require a live Riak instance.</dd>
<dt>Functional Tests</dt>
<dd>Focus is on the functionality the user is likely to regularly execute, e.g. storing an object. These tests will connect to and use a live Riak instance.</dd>
<dt>Scenario Tests</dt>
<dd>Focus is on testing how the library and Riak respond to edge cases, e.g. how it handles when a node becomes unreachable. These tests will connect to and use a live Riak instance.</dd>
</dl>

#### Running Tests
We also expect that before submitting a pull request, that you have run the tests to ensure that all of them continue to pass after your changes.

To run the tests, clone this repository and run `composer update` from the repository root, then you can execute all the tests by simply running `php vendor/bin/phpunit`. To execute only a single group of tests, you can use the "testsuite" argument, e.g. `php vendor/bin/phpunit --testsuite 'Unit Tests'`.

You can execute code coverage analysis along side the test run by appending ` --coverage-text` to the command above.

Please note, that the Functional and Scenario tests require a live Riak instance to test against. Also, the following bucket types to be created and activated on the Riak instance. If using the [riak-clients-vagrant](https://github.com/basho-labs/riak-clients-vagrant) project, the `integration_testing` role creates these bucket types for you.

```bash
riak-admin bucket-type create counters '{"props":{"datatype":"counter"}}'
riak-admin bucket-type create sets '{"props":{"datatype":"set"}}'
riak-admin bucket-type create maps '{"props":{"datatype":"map"}}'
riak-admin bucket-type create yokozuna '{"props":{}}'

riak-admin bucket-type activate counters
riak-admin bucket-type activate sets
riak-admin bucket-type activate maps
riak-admin bucket-type activate yokozuna
```

## Thank You

You can [read the full guidelines for bug reporting and code contributions](http://docs.basho.com/riak/latest/community/bugs/) on the Riak Docs. 

And **thank you!** Your contribution is incredible important to us.
