Instructions to import/update aws-sdk-php library into Moodle:

Update aws-sdk-php library
1. Download the latest aws-sdk-php library package from https://github.com/aws/aws-sdk-php
2. Copy the src directory to lib/aws-sdk/src folder
3. Copy the associated files LICENCE, README.md etc. to aws-sdk directory

This Moodle copy is customised for MDL-87598 (CVE-2025-14761) to backport
AWS SDK for PHP S3 key commitment validation.
