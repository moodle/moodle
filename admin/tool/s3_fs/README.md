# S3 File System for Moodle

This plugin allows for using Amazon's S3 service as a file system for Moodle's hashed
file directory.

# Migration

To migrate to the S3 file system, first you must sync the `moodledata/filedir` into the
S3 bucket, under the `filedir` folder.  You can optionally specify a root folder in the
configuration, if you do, then sync the files to `your_root/filedir` in the S3 bucket.

Once the files have been synced, update the Moodle config file with the configs in the next
section.

# Configuration

Example minimum configuration that you add to your Moodle config file:

    $CFG->alternative_file_system_class = '\tool_s3_fs\file_system';
    $CFG->tool_s3_fs = [
        'bucket' => 'bucket_name',
        'region' => 'us-east-2',
    ];

The following options are supported:

* **bucket**: (Required, string) This is the name of the S3 bucket.
* **region**: (Required, string) This is the name of the AWS region that the S3 bucket resides in.
* **key**: (Optional, string) Credential key to access the S3 bucket.
* **secret**: (Optional, string) Credential secret to access the S3 bucket.
* **credentials_cache**: (Optional, string) Used when key/secret are not set and using IAM roles for EC2 instances.
  Valid value is `apcu` which is not usable on the CLI.
* **folder**: (Optional, string, default no folder) Place `filedir` under this folder.
* **delete**: (Optional, boolean, default `true`) Allow files to be deleted from the S3 bucket.
* **gzstream**: (Optional, boolean, default `true`) Stream gz-files from S3. If false, then will download
  the gz-file to disk before opening it.

A note about credentials.  If `key`/`secret` are not used, then the AWS SDK will fallback
to various means to find credentials. See
[credentials configuration option](http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html#credentials)
for more details.