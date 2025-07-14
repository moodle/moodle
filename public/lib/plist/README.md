# CFPropertyList

[![License MIT](https://img.shields.io/badge/License-MIT-blue.svg)](./LICENSE.md)
[![Project Status: Active](http://www.repostatus.org/badges/latest/active.svg)](http://www.repostatus.org/#active)
[![Conventional Commits](https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg)](https://conventionalcommits.org)

## Table of Contents

* [Synopsis](#synopsis)
* [Build Status](#build-status)
* [Installation](#installation)
* [Documentation](#documentation)
* [Versioning](#versioning)
* [Contact](#contact)
* [Contribute](#contribute)
* [Copying](#copying)

## Synopsis

The PHP implementation of Apple's PropertyList can handle both XML and binary PropertyLists. It offers functionality to easily convert data between worlds, e.g. recalculating timestamps from unix epoch to apple epoch and vice versa. A feature to automagically create (guess) the plist structure from a normal PHP data structure will help you dump your data to plist in no time.

CFPropertyList does not rely on any "Apple proprietary" components, like plutil. CFPropertyList runs on any Operating System with PHP and some standard extensions installed.

Although you might want to deliver data to your iPhone application, you might want to run those server side services on your standard Linux (or even Windows) environment, rather than buying an expensive Apple Server. With CFPropertyList you now have the power to provide data from your favorite Operating System.

## Build Status

|**Release channel**|Beta Channel|
|:---:|:---:|
|![unit tests](https://github.com/moodlehq/CFPropertyList/actions/workflows/run_tests.yml/badge.svg?branch=master)|![unit tests](https://github.com/moodlehq/CFPropertyList/actions/workflows/run_tests.yml/badge.svg?branch=develop)|

## Installation

See or [How to install article](https://moodlehq.github.io/CFPropertyList/howtos/installation).

## Documentation

We maintain a detailed documentation of the project on the Website, check the Development [Development](https://moodlehq.github.io/CFPropertyList/) and [How-tos](https://moodlehq.github.io/CFPropertyList/howtos) sections.

## Versioning

In order to provide transparency on our release cycle and to maintain backward compatibility, Flyve MDM is maintained under [the Semantic Versioning guidelines](http://semver.org/). We are committed to following and complying with the rules, the best we can.

See [the tags section of our GitHub project](https://github.com/moodlehq/CFPropertyList/tags) for changelogs for each release version.

## Contact

You can contact us through any of our channels, check our [Support channels](https://moodledev.io/general/channels)

## Contribute

Want to file a bug, contribute some code, or improve documentation? Excellent! Read up on our
guidelines for [contributing](./CONTRIBUTING.md) and then check out one of our issues in the [Issues Dashboard](https://github.com/moodlehq/CFPropertyList/issues).

## Copying

* **Code**: you can redistribute it and/or modify
    it under the terms of the MIT License ([MIT](https://opensource.org/licenses/MIT)).
* **Documentation**: released under Attribution 4.0 International ([CC BY 4.0](https://creativecommons.org/licenses/by/4.0/)).

## History

This repository was previously maintained by [Teclib](https://github.com/TECLIB/CFPropertyList/) but was archived. We thanks Teclib for their years of work work maintaining this project
