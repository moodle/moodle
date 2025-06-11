# AWS SDK for PHP - Version 3

[![Total Downloads](https://img.shields.io/packagist/dt/aws/aws-sdk-php.svg?style=flat)](https://packagist.org/packages/aws/aws-sdk-php)
[![Apache 2 License](https://img.shields.io/packagist/l/aws/aws-sdk-php.svg?style=flat)](http://aws.amazon.com/apache-2-0/)
[![Gitter](https://badges.gitter.im/aws/aws-sdk-php.svg)](https://gitter.im/aws/aws-sdk-php?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![codecov](https://codecov.io/gh/aws/aws-sdk-php/branch/master/graph/badge.svg)](https://codecov.io/gh/aws/aws-sdk-php)

The **AWS SDK for PHP** makes it easy for developers to access [Amazon Web
Services][aws] in their PHP code, and build robust applications and software
using services like Amazon S3, Amazon DynamoDB, Amazon Glacier, etc. You can
get started in minutes by [installing the SDK through Composer][docs-installation]
or by downloading a single zip or phar file from our [latest release][latest-release].

Jump To:
- [AWS SDK for PHP - Version 3](#aws-sdk-for-php---version-3)
  - [Getting Started](#getting-started)
  - [Quick Examples](#quick-examples)
    - [Create an Amazon S3 client](#create-an-amazon-s3-client)
    - [Upload a file to Amazon S3](#upload-a-file-to-amazon-s3)
  - [Getting Help](#getting-help)
  - [Maintenance and support for SDK major versions](#maintenance-and-support-for-sdk-major-versions)
    - [Opening Issues](#opening-issues)
  - [Features](#features)
  - [Contributing](#contributing)
  - [Resources](#resources)
  - [Related AWS Projects](#related-aws-projects)

## Getting Started

1. **Sign up for AWS** – Before you begin, you need to
   sign up for an AWS account and retrieve your [AWS credentials][docs-signup].
2. **Minimum requirements** – To run the SDK, your system will need to meet the
   [minimum requirements][docs-requirements], including having **PHP >= 7.2.5**.
   We highly recommend having it compiled with the cURL extension and cURL
   7.16.2+ compiled with a TLS backend (e.g., NSS or OpenSSL).
3. **Install the SDK** – Using [Composer] is the recommended way to install the
   AWS SDK for PHP. The SDK is available via [Packagist] under the
   [`aws/aws-sdk-php`][install-packagist] package. If Composer is installed globally on your system, you can run the following in the base directory of your project to add the SDK as a dependency:
   ```
   composer require aws/aws-sdk-php
   ```
   Please see the
   [Installation section of the User Guide][docs-installation] for more
   detailed information about installing the SDK through Composer and other
   means.
4. **Using the SDK** – The best way to become familiar with how to use the SDK
   is to read the [User Guide][docs-guide]. The
   [Getting Started Guide][docs-quickstart] will help you become familiar with
   the basic concepts.
5. **Beta: Removing unused services** — To date, there are over 300 AWS services available for use with this SDK.
   You will likely not need them all. If you use Composer and would like to learn more about this feature,
    please read the [linked documentation][docs-script-composer].


## Quick Examples

### Create an Amazon S3 client

```php
<?php
// Require the Composer autoloader.
require 'vendor/autoload.php';

use Aws\S3\S3Client;

// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
```

### Upload a file to Amazon S3

```php
<?php
// Upload a publicly accessible file. The file size and type are determined by the SDK.
try {
    $s3->putObject([
        'Bucket' => 'my-bucket',
        'Key'    => 'my-object',
        'Body'   => fopen('/path/to/file', 'r'),
        'ACL'    => 'public-read',
    ]);
} catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error uploading the file.\n";
}
```

## Getting Help

Please use these community resources for getting help. We use the GitHub issues for tracking bugs and feature requests and have limited bandwidth to address them.

* Ask a question on [StackOverflow](https://stackoverflow.com/) and tag it with [`aws-php-sdk`](http://stackoverflow.com/questions/tagged/aws-php-sdk)
* Come join the AWS SDK for PHP [gitter](https://gitter.im/aws/aws-sdk-php)
* Open a support ticket with [AWS Support](https://console.aws.amazon.com/support/home/)
* If it turns out that you may have found a bug, please [open an issue](https://github.com/aws/aws-sdk-php/issues/new/choose)

This SDK implements AWS service APIs. For general issues regarding the AWS services and their limitations, you may also take a look at the [Amazon Web Services Discussion Forums](https://forums.aws.amazon.com/).


## Maintenance and support for SDK major versions

For information about maintenance and support for SDK major versions and their underlying dependencies, see the following in the [AWS SDKs and Tools Shared Configuration and Credentials Reference Guide](https://docs.aws.amazon.com/credref/latest/refdocs/overview.html):

* [AWS SDKs and Tools Maintenance Policy](https://docs.aws.amazon.com/credref/latest/refdocs/maint-policy.html)
* [AWS SDKs and Tools Version Support Matrix](https://docs.aws.amazon.com/credref/latest/refdocs/version-support-matrix.html)


### Opening Issues

If you encounter a bug with `aws-sdk-php` we would like to hear about it. Search the existing issues and try to make sure your problem doesn’t already exist before opening a new issue. It’s helpful if you include the version of `aws-sdk-php`, PHP version and OS you’re using. Please include a stack trace and a simple workflow to reproduce the case when appropriate, too.

The GitHub issues are intended for bug reports and feature requests. For help and questions with using `aws-sdk-php` please make use of the resources listed in the Getting Help section. There are limited resources available for handling issues and by keeping the list of open issues lean we can respond in a timely manner.

## Features

* Provides easy-to-use HTTP clients for all supported AWS
  [services][docs-services], [regions][docs-rande], and authentication
  protocols.
* Is built on [Guzzle][guzzle-docs], and utilizes many of its features,
  including persistent connections, asynchronous requests, middlewares, etc.
* Provides convenience features including easy result pagination via
  [Paginators][docs-paginators], [Waiters][docs-waiters], and simple
  [Result objects][docs-results].
* Provides a [multipart uploader tool][docs-s3-multipart] for Amazon S3 and
  Amazon Glacier that can be paused and resumed.
* Provides an [Amazon S3 Stream Wrapper][docs-streamwrapper], so that you can
  use PHP's native file handling functions to interact with your S3 buckets and
  objects like a local filesystem.
* Provides an [Amazon S3 Encryption Client][docs-s3-encryption] for creating and interacting with encrypted objects in your S3 buckets.
* Provides the [Amazon DynamoDB Session Handler][docs-ddbsh] for easily scaling
  sessions on a fast, NoSQL database.
* Automatically uses [IAM Instance Profile Credentials][aws-iam-credentials] on
  configured Amazon EC2 instances.

## Contributing

We work hard to provide a high-quality and useful SDK for our AWS services, and we greatly value feedback and contributions from our community. Please review our [contributing guidelines](./CONTRIBUTING.md) before submitting any issues or pull requests to ensure we have all the necessary information to effectively respond to your bug report or contribution.


## Resources

* [User Guide][docs-guide] – For both getting started and in-depth SDK usage information
* [API Docs][docs-api] – For details about operations, parameters, and responses
* [Blog][sdk-blog] – Tips & tricks, articles, and announcements
* [Sample Project][sdk-sample] - A quick, sample project to help get you started
* [Forum][sdk-forum] – Ask questions, get help, and give feedback
* [Issues][sdk-issues] – Report issues, submit pull requests, and get involved
  (see [Apache 2.0 License][sdk-license])

## Related AWS Projects

* [AWS Service Provider for Laravel][mod-laravel]
* [AWS SDK ZF2 Module][mod-zf2]
* [AWS Service Provider for Silex][mod-silex]
* [AWS SDK Bundle for Symfony][mod-symfony]
* [Amazon SNS Message Validator for PHP][sns-validator] - SNS validator without requiring SDK
* [Guzzle Version 7][guzzle-docs] – PHP HTTP client and framework
* For Version 2 of the SDK (deprecated):
  * [User Guide][docs-guide-v2]
  * [API Docs][docs-api-v2]
* [Serverless LAMP stack guide][serverless-LAMP-stack-guide] - A guide to building and deploying a serverless PHP application
* Other [AWS SDKs & Tools][aws-tools] (e.g., js, cli, ruby, python, java, etc.)

[sdk-website]: http://aws.amazon.com/sdkforphp
[sdk-forum]: https://forums.aws.amazon.com/forum.jspa?forumID=80
[sdk-issues]: https://github.com/aws/aws-sdk-php/issues
[sdk-license]: http://aws.amazon.com/apache2.0/
[sdk-blog]: https://aws.amazon.com/blogs/developer/category/php/
[sdk-sample]: http://aws.amazon.com/developers/getting-started/php

[install-packagist]: https://packagist.org/packages/aws/aws-sdk-php
[latest-release]: https://github.com/aws/aws-sdk-php/releases

[docs-api]: http://docs.aws.amazon.com/aws-sdk-php/v3/api/index.html
[docs-guide]: http://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/welcome.html
[docs-api-v2]: http://docs.aws.amazon.com/aws-sdk-php/v2/api/index.html
[docs-guide-v2]: http://docs.aws.amazon.com/aws-sdk-php/v2/guide/index.html
[docs-contribution]: https://github.com/aws/aws-sdk-php/blob/master/CONTRIBUTING.md
[docs-migration]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_migration.html
[docs-signup]: https://aws.amazon.com/premiumsupport/knowledge-center/create-access-key/
[docs-requirements]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_requirements.html
[docs-installation]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html
[docs-quickstart]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/welcome.html#getting-started
[docs-paginators]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_paginators.html
[docs-waiters]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_waiters.html
[docs-results]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_basic-usage.html#result-objects
[docs-exceptions]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_basic-usage.html#handling-errors
[docs-wire-logging]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/faq.html#how-can-i-see-what-data-is-sent-over-the-wire
[docs-ddbsh]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/service_dynamodb-session-handler.html
[docs-services]: https://aws.amazon.com/products/
[docs-rande]: http://docs.aws.amazon.com/general/latest/gr/rande.html
[docs-streamwrapper]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-stream-wrapper.html
[docs-s3-transfer]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-transfer.html
[docs-s3-multipart]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-multipart-upload.html
[docs-s3-encryption]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-encryption-client.html
[docs-script-composer]: https://github.com/aws/aws-sdk-php/tree/master/src/Script/Composer

[aws]: http://aws.amazon.com
[aws-iam-credentials]: http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/UsingIAM.html#UsingIAMrolesWithAmazonEC2Instances
[aws-tools]: http://aws.amazon.com/tools
[guzzle-docs]: http://guzzlephp.org
[composer]: http://getcomposer.org
[packagist]: http://packagist.org
[psr-7]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md
[psr-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[psr-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[psr-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

[mod-laravel]: https://github.com/aws/aws-sdk-php-laravel
[mod-zf2]: https://github.com/aws/aws-sdk-php-zf2
[mod-silex]: https://github.com/aws/aws-sdk-php-silex
[mod-symfony]: https://github.com/aws/aws-sdk-php-symfony
[sns-validator]: https://github.com/aws/aws-php-sns-message-validator
[serverless-LAMP-stack-guide]: https://github.com/aws-samples/php-examples-for-aws-lambda
