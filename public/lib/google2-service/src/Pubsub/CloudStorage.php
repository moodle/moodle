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

namespace Google\Service\Pubsub;

class CloudStorage extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Ingestion is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Permission denied encountered while calling the Cloud Storage API. This can
   * happen if the Pub/Sub SA has not been granted the [appropriate
   * permissions](https://cloud.google.com/storage/docs/access-control/iam-
   * permissions): - storage.objects.list: to list the objects in a bucket. -
   * storage.objects.get: to read the objects in a bucket. -
   * storage.buckets.get: to verify the bucket exists.
   */
  public const STATE_CLOUD_STORAGE_PERMISSION_DENIED = 'CLOUD_STORAGE_PERMISSION_DENIED';
  /**
   * Permission denied encountered while publishing to the topic. This can
   * happen if the Pub/Sub SA has not been granted the [appropriate publish
   * permissions](https://cloud.google.com/pubsub/docs/access-
   * control#pubsub.publisher)
   */
  public const STATE_PUBLISH_PERMISSION_DENIED = 'PUBLISH_PERMISSION_DENIED';
  /**
   * The provided Cloud Storage bucket doesn't exist.
   */
  public const STATE_BUCKET_NOT_FOUND = 'BUCKET_NOT_FOUND';
  /**
   * The Cloud Storage bucket has too many objects, ingestion will be paused.
   */
  public const STATE_TOO_MANY_OBJECTS = 'TOO_MANY_OBJECTS';
  protected $avroFormatType = AvroFormat::class;
  protected $avroFormatDataType = '';
  /**
   * Optional. Cloud Storage bucket. The bucket name must be without any prefix
   * like "gs://". See the [bucket naming requirements]
   * (https://cloud.google.com/storage/docs/buckets#naming).
   *
   * @var string
   */
  public $bucket;
  /**
   * Optional. Glob pattern used to match objects that will be ingested. If
   * unset, all objects will be ingested. See the [supported patterns](https://c
   * loud.google.com/storage/docs/json_api/v1/objects/list#list-objects-and-
   * prefixes-using-glob).
   *
   * @var string
   */
  public $matchGlob;
  /**
   * Optional. Only objects with a larger or equal creation timestamp will be
   * ingested.
   *
   * @var string
   */
  public $minimumObjectCreateTime;
  protected $pubsubAvroFormatType = PubSubAvroFormat::class;
  protected $pubsubAvroFormatDataType = '';
  /**
   * Output only. An output-only field that indicates the state of the Cloud
   * Storage ingestion source.
   *
   * @var string
   */
  public $state;
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';

  /**
   * Optional. Data from Cloud Storage will be interpreted in Avro format.
   *
   * @param AvroFormat $avroFormat
   */
  public function setAvroFormat(AvroFormat $avroFormat)
  {
    $this->avroFormat = $avroFormat;
  }
  /**
   * @return AvroFormat
   */
  public function getAvroFormat()
  {
    return $this->avroFormat;
  }
  /**
   * Optional. Cloud Storage bucket. The bucket name must be without any prefix
   * like "gs://". See the [bucket naming requirements]
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
   * Optional. Glob pattern used to match objects that will be ingested. If
   * unset, all objects will be ingested. See the [supported patterns](https://c
   * loud.google.com/storage/docs/json_api/v1/objects/list#list-objects-and-
   * prefixes-using-glob).
   *
   * @param string $matchGlob
   */
  public function setMatchGlob($matchGlob)
  {
    $this->matchGlob = $matchGlob;
  }
  /**
   * @return string
   */
  public function getMatchGlob()
  {
    return $this->matchGlob;
  }
  /**
   * Optional. Only objects with a larger or equal creation timestamp will be
   * ingested.
   *
   * @param string $minimumObjectCreateTime
   */
  public function setMinimumObjectCreateTime($minimumObjectCreateTime)
  {
    $this->minimumObjectCreateTime = $minimumObjectCreateTime;
  }
  /**
   * @return string
   */
  public function getMinimumObjectCreateTime()
  {
    return $this->minimumObjectCreateTime;
  }
  /**
   * Optional. It will be assumed data from Cloud Storage was written via [Cloud
   * Storage subscriptions](https://cloud.google.com/pubsub/docs/cloudstorage).
   *
   * @param PubSubAvroFormat $pubsubAvroFormat
   */
  public function setPubsubAvroFormat(PubSubAvroFormat $pubsubAvroFormat)
  {
    $this->pubsubAvroFormat = $pubsubAvroFormat;
  }
  /**
   * @return PubSubAvroFormat
   */
  public function getPubsubAvroFormat()
  {
    return $this->pubsubAvroFormat;
  }
  /**
   * Output only. An output-only field that indicates the state of the Cloud
   * Storage ingestion source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE,
   * CLOUD_STORAGE_PERMISSION_DENIED, PUBLISH_PERMISSION_DENIED,
   * BUCKET_NOT_FOUND, TOO_MANY_OBJECTS
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Data from Cloud Storage will be interpreted as text.
   *
   * @param TextFormat $textFormat
   */
  public function setTextFormat(TextFormat $textFormat)
  {
    $this->textFormat = $textFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTextFormat()
  {
    return $this->textFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudStorage::class, 'Google_Service_Pubsub_CloudStorage');
