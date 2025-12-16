<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\AnalyticsHub;

class CloudStorageConfig extends \Google\Model
{
  protected $avroConfigType = AvroConfig::class;
  protected $avroConfigDataType = '';
  /**
   * Required. User-provided name for the Cloud Storage bucket. The bucket must
   * be created by the user. The bucket name must be without any prefix like
   * "gs://". See the [bucket naming requirements]
   * (https://cloud.google.com/storage/docs/buckets#naming).
   *
   * @var string
   */
  public $bucket;
  /**
   * Optional. User-provided format string specifying how to represent datetimes
   * in Cloud Storage filenames. See the [datetime format
   * guidance](https://cloud.google.com/pubsub/docs/create-cloudstorage-
   * subscription#file_names).
   *
   * @var string
   */
  public $filenameDatetimeFormat;
  /**
   * Optional. User-provided prefix for Cloud Storage filename. See the [object
   * naming requirements](https://cloud.google.com/storage/docs/objects#naming).
   *
   * @var string
   */
  public $filenamePrefix;
  /**
   * Optional. User-provided suffix for Cloud Storage filename. See the [object
   * naming requirements](https://cloud.google.com/storage/docs/objects#naming).
   * Must not end in "/".
   *
   * @var string
   */
  public $filenameSuffix;
  /**
   * Optional. The maximum bytes that can be written to a Cloud Storage file
   * before a new file is created. Min 1 KB, max 10 GiB. The max_bytes limit may
   * be exceeded in cases where messages are larger than the limit.
   *
   * @var string
   */
  public $maxBytes;
  /**
   * Optional. File batching settings. If no max_duration setting is specified,
   * a max_duration of 5 minutes will be set by default. max_duration is
   * required regardless of whether other file batching settings are specified.
   * The maximum duration that can elapse before a new Cloud Storage file is
   * created. Min 1 minute, max 10 minutes, default 5 minutes. May not exceed
   * the subscription's acknowledgement deadline.
   *
   * @var string
   */
  public $maxDuration;
  /**
   * Optional. The maximum number of messages that can be written to a Cloud
   * Storage file before a new file is created. Min 1000 messages.
   *
   * @var string
   */
  public $maxMessages;
  /**
   * Optional. The service account to use to write to Cloud Storage. The
   * subscription creator or updater that specifies this field must have
   * `iam.serviceAccounts.actAs` permission on the service account. If not
   * specified, the Pub/Sub [service
   * agent](https://cloud.google.com/iam/docs/service-agents),
   * service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com, is used.
   *
   * @var string
   */
  public $serviceAccountEmail;
  protected $textConfigType = TextConfig::class;
  protected $textConfigDataType = '';

  /**
   * Optional. If set, message data will be written to Cloud Storage in Avro
   * format.
   *
   * @param AvroConfig $avroConfig
   */
  public function setAvroConfig(AvroConfig $avroConfig)
  {
    $this->avroConfig = $avroConfig;
  }
  /**
   * @return AvroConfig
   */
  public function getAvroConfig()
  {
    return $this->avroConfig;
  }
  /**
   * Required. User-provided name for the Cloud Storage bucket. The bucket must
   * be created by the user. The bucket name must be without any prefix like
   * "gs://". See the [bucket naming requirements]
   * (https://cloud.google.com/storage/docs/buckets#naming).
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Optional. User-provided format string specifying how to represent datetimes
   * in Cloud Storage filenames. See the [datetime format
   * guidance](https://cloud.google.com/pubsub/docs/create-cloudstorage-
   * subscription#file_names).
   *
   * @param string $filenameDatetimeFormat
   */
  public function setFilenameDatetimeFormat($filenameDatetimeFormat)
  {
    $this->filenameDatetimeFormat = $filenameDatetimeFormat;
  }
  /**
   * @return string
   */
  public function getFilenameDatetimeFormat()
  {
    return $this->filenameDatetimeFormat;
  }
  /**
   * Optional. User-provided prefix for Cloud Storage filename. See the [object
   * naming requirements](https://cloud.google.com/storage/docs/objects#naming).
   *
   * @param string $filenamePrefix
   */
  public function setFilenamePrefix($filenamePrefix)
  {
    $this->filenamePrefix = $filenamePrefix;
  }
  /**
   * @return string
   */
  public function getFilenamePrefix()
  {
    return $this->filenamePrefix;
  }
  /**
   * Optional. User-provided suffix for Cloud Storage filename. See the [object
   * naming requirements](https://cloud.google.com/storage/docs/objects#naming).
   * Must not end in "/".
   *
   * @param string $filenameSuffix
   */
  public function setFilenameSuffix($filenameSuffix)
  {
    $this->filenameSuffix = $filenameSuffix;
  }
  /**
   * @return string
   */
  public function getFilenameSuffix()
  {
    return $this->filenameSuffix;
  }
  /**
   * Optional. The maximum bytes that can be written to a Cloud Storage file
   * before a new file is created. Min 1 KB, max 10 GiB. The max_bytes limit may
   * be exceeded in cases where messages are larger than the limit.
   *
   * @param string $maxBytes
   */
  public function setMaxBytes($maxBytes)
  {
    $this->maxBytes = $maxBytes;
  }
  /**
   * @return string
   */
  public function getMaxBytes()
  {
    return $this->maxBytes;
  }
  /**
   * Optional. File batching settings. If no max_duration setting is specified,
   * a max_duration of 5 minutes will be set by default. max_duration is
   * required regardless of whether other file batching settings are specified.
   * The maximum duration that can elapse before a new Cloud Storage file is
   * created. Min 1 minute, max 10 minutes, default 5 minutes. May not exceed
   * the subscription's acknowledgement deadline.
   *
   * @param string $maxDuration
   */
  public function setMaxDuration($maxDuration)
  {
    $this->maxDuration = $maxDuration;
  }
  /**
   * @return string
   */
  public function getMaxDuration()
  {
    return $this->maxDuration;
  }
  /**
   * Optional. The maximum number of messages that can be written to a Cloud
   * Storage file before a new file is created. Min 1000 messages.
   *
   * @param string $maxMessages
   */
  public function setMaxMessages($maxMessages)
  {
    $this->maxMessages = $maxMessages;
  }
  /**
   * @return string
   */
  public function getMaxMessages()
  {
    return $this->maxMessages;
  }
  /**
   * Optional. The service account to use to write to Cloud Storage. The
   * subscription creator or updater that specifies this field must have
   * `iam.serviceAccounts.actAs` permission on the service account. If not
   * specified, the Pub/Sub [service
   * agent](https://cloud.google.com/iam/docs/service-agents),
   * service-{project_number}@gcp-sa-pubsub.iam.gserviceaccount.com, is used.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Optional. If set, message data will be written to Cloud Storage in text
   * format.
   *
   * @param TextConfig $textConfig
   */
  public function setTextConfig(TextConfig $textConfig)
  {
    $this->textConfig = $textConfig;
  }
  /**
   * @return TextConfig
   */
  public function getTextConfig()
  {
    return $this->textConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudStorageConfig::class, 'Google_Service_AnalyticsHub_CloudStorageConfig');
