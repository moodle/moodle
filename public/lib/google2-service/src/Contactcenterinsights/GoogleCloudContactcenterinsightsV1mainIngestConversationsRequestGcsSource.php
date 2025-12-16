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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainIngestConversationsRequestGcsSource extends \Google\Collection
{
  /**
   * The object type is unspecified and will default to `TRANSCRIPT`.
   */
  public const BUCKET_OBJECT_TYPE_BUCKET_OBJECT_TYPE_UNSPECIFIED = 'BUCKET_OBJECT_TYPE_UNSPECIFIED';
  /**
   * The object is a transcript.
   */
  public const BUCKET_OBJECT_TYPE_TRANSCRIPT = 'TRANSCRIPT';
  /**
   * The object is an audio file.
   */
  public const BUCKET_OBJECT_TYPE_AUDIO = 'AUDIO';
  protected $collection_key = 'customMetadataKeys';
  /**
   * Optional. The Cloud Storage path to the conversation audio file. Note that:
   * [1] Audio files will be transcribed if not already. [2] Audio files and
   * transcript files must be in separate buckets / folders. [3] A source file
   * and its corresponding audio file must share the same name to be properly
   * ingested, E.g. `gs://bucket/transcript/conversation1.json` and
   * `gs://bucket/audio/conversation1.mp3`.
   *
   * @var string
   */
  public $audioBucketUri;
  /**
   * Optional. Specifies the type of the objects in `bucket_uri`. Avoid passing
   * this. This is inferred from the `transcript_bucket_uri`,
   * `audio_bucket_uri`.
   *
   * @var string
   */
  public $bucketObjectType;
  /**
   * Optional. The Cloud Storage bucket containing source objects. Avoid passing
   * this. Pass this through one of `transcript_bucket_uri` or
   * `audio_bucket_uri`.
   *
   * @var string
   */
  public $bucketUri;
  /**
   * Optional. Custom keys to extract as conversation labels from metadata files
   * in `metadata_bucket_uri`. Keys not included in this field will be ignored.
   * Note that there is a limit of 100 labels per conversation.
   *
   * @var string[]
   */
  public $customMetadataKeys;
  /**
   * Optional. The Cloud Storage path to the conversation metadata. Note that:
   * [1] Metadata files are expected to be in JSON format. [2] Metadata and
   * source files (transcripts or audio) must be in separate buckets / folders.
   * [3] A source file and its corresponding metadata file must share the same
   * name to be properly ingested, E.g. `gs://bucket/audio/conversation1.mp3`
   * and `gs://bucket/metadata/conversation1.json`.
   *
   * @var string
   */
  public $metadataBucketUri;
  /**
   * Optional. The Cloud Storage path to the conversation transcripts. Note
   * that: [1] Transcript files are expected to be in JSON format. [2]
   * Transcript, audio, metadata files must be in separate buckets / folders.
   * [3] A source file and its corresponding metadata file must share the same
   * name to be properly ingested, E.g. `gs://bucket/audio/conversation1.mp3`
   * and `gs://bucket/metadata/conversation1.json`.
   *
   * @var string
   */
  public $transcriptBucketUri;

  /**
   * Optional. The Cloud Storage path to the conversation audio file. Note that:
   * [1] Audio files will be transcribed if not already. [2] Audio files and
   * transcript files must be in separate buckets / folders. [3] A source file
   * and its corresponding audio file must share the same name to be properly
   * ingested, E.g. `gs://bucket/transcript/conversation1.json` and
   * `gs://bucket/audio/conversation1.mp3`.
   *
   * @param string $audioBucketUri
   */
  public function setAudioBucketUri($audioBucketUri)
  {
    $this->audioBucketUri = $audioBucketUri;
  }
  /**
   * @return string
   */
  public function getAudioBucketUri()
  {
    return $this->audioBucketUri;
  }
  /**
   * Optional. Specifies the type of the objects in `bucket_uri`. Avoid passing
   * this. This is inferred from the `transcript_bucket_uri`,
   * `audio_bucket_uri`.
   *
   * Accepted values: BUCKET_OBJECT_TYPE_UNSPECIFIED, TRANSCRIPT, AUDIO
   *
   * @param self::BUCKET_OBJECT_TYPE_* $bucketObjectType
   */
  public function setBucketObjectType($bucketObjectType)
  {
    $this->bucketObjectType = $bucketObjectType;
  }
  /**
   * @return self::BUCKET_OBJECT_TYPE_*
   */
  public function getBucketObjectType()
  {
    return $this->bucketObjectType;
  }
  /**
   * Optional. The Cloud Storage bucket containing source objects. Avoid passing
   * this. Pass this through one of `transcript_bucket_uri` or
   * `audio_bucket_uri`.
   *
   * @param string $bucketUri
   */
  public function setBucketUri($bucketUri)
  {
    $this->bucketUri = $bucketUri;
  }
  /**
   * @return string
   */
  public function getBucketUri()
  {
    return $this->bucketUri;
  }
  /**
   * Optional. Custom keys to extract as conversation labels from metadata files
   * in `metadata_bucket_uri`. Keys not included in this field will be ignored.
   * Note that there is a limit of 100 labels per conversation.
   *
   * @param string[] $customMetadataKeys
   */
  public function setCustomMetadataKeys($customMetadataKeys)
  {
    $this->customMetadataKeys = $customMetadataKeys;
  }
  /**
   * @return string[]
   */
  public function getCustomMetadataKeys()
  {
    return $this->customMetadataKeys;
  }
  /**
   * Optional. The Cloud Storage path to the conversation metadata. Note that:
   * [1] Metadata files are expected to be in JSON format. [2] Metadata and
   * source files (transcripts or audio) must be in separate buckets / folders.
   * [3] A source file and its corresponding metadata file must share the same
   * name to be properly ingested, E.g. `gs://bucket/audio/conversation1.mp3`
   * and `gs://bucket/metadata/conversation1.json`.
   *
   * @param string $metadataBucketUri
   */
  public function setMetadataBucketUri($metadataBucketUri)
  {
    $this->metadataBucketUri = $metadataBucketUri;
  }
  /**
   * @return string
   */
  public function getMetadataBucketUri()
  {
    return $this->metadataBucketUri;
  }
  /**
   * Optional. The Cloud Storage path to the conversation transcripts. Note
   * that: [1] Transcript files are expected to be in JSON format. [2]
   * Transcript, audio, metadata files must be in separate buckets / folders.
   * [3] A source file and its corresponding metadata file must share the same
   * name to be properly ingested, E.g. `gs://bucket/audio/conversation1.mp3`
   * and `gs://bucket/metadata/conversation1.json`.
   *
   * @param string $transcriptBucketUri
   */
  public function setTranscriptBucketUri($transcriptBucketUri)
  {
    $this->transcriptBucketUri = $transcriptBucketUri;
  }
  /**
   * @return string
   */
  public function getTranscriptBucketUri()
  {
    return $this->transcriptBucketUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainIngestConversationsRequestGcsSource::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainIngestConversationsRequestGcsSource');
