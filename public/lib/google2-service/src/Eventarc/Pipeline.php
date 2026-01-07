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

namespace Google\Service\Eventarc;

class Pipeline extends \Google\Collection
{
  protected $collection_key = 'mediations';
  /**
   * Optional. User-defined annotations. See
   * https://google.aip.dev/128#annotations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The creation time. A timestamp in RFC3339 UTC "Zulu" format,
   * with nanosecond resolution and up to nine fractional digits. Examples:
   * "2014-10-02T15:01:23Z" and "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt the event data. If not set, an internal Google-owned key
   * will be used to encrypt messages. It must match the pattern "projects/{proj
   * ect}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}".
   *
   * @var string
   */
  public $cryptoKeyName;
  protected $destinationsType = GoogleCloudEventarcV1PipelineDestination::class;
  protected $destinationsDataType = 'array';
  /**
   * Optional. Display name of resource.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on create requests to ensure that the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  protected $inputPayloadFormatType = GoogleCloudEventarcV1PipelineMessagePayloadFormat::class;
  protected $inputPayloadFormatDataType = '';
  /**
   * Optional. User labels attached to the Pipeline that can be used to group
   * resources. An object containing a list of "key": value pairs. Example: {
   * "name": "wrench", "mass": "1.3kg", "count": "3" }.
   *
   * @var string[]
   */
  public $labels;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  protected $mediationsType = GoogleCloudEventarcV1PipelineMediation::class;
  protected $mediationsDataType = 'array';
  /**
   * Identifier. The resource name of the Pipeline. Must be unique within the
   * location of the project and must be in
   * `projects/{project}/locations/{location}/pipelines/{pipeline}` format.
   *
   * @var string
   */
  public $name;
  protected $retryPolicyType = GoogleCloudEventarcV1PipelineRetryPolicy::class;
  protected $retryPolicyDataType = '';
  /**
   * Output only. Whether or not this Pipeline satisfies the requirements of
   * physical zone separation
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Server-assigned unique identifier for the Pipeline. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time. A timestamp in RFC3339 UTC "Zulu"
   * format, with nanosecond resolution and up to nine fractional digits.
   * Examples: "2014-10-02T15:01:23Z" and "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User-defined annotations. See
   * https://google.aip.dev/128#annotations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The creation time. A timestamp in RFC3339 UTC "Zulu" format,
   * with nanosecond resolution and up to nine fractional digits. Examples:
   * "2014-10-02T15:01:23Z" and "2014-10-02T15:01:23.045123456Z".
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Resource name of a KMS crypto key (managed by the user) used to
   * encrypt/decrypt the event data. If not set, an internal Google-owned key
   * will be used to encrypt messages. It must match the pattern "projects/{proj
   * ect}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}".
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Required. List of destinations to which messages will be forwarded.
   * Currently, exactly one destination is supported per Pipeline.
   *
   * @param GoogleCloudEventarcV1PipelineDestination[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineDestination[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Optional. Display name of resource.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and might be sent only on create requests to ensure that the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The payload format expected for the messages received by the
   * Pipeline. If input_payload_format is set then any messages not matching
   * this format will be treated as persistent errors. If input_payload_format
   * is not set, then the message data will be treated as an opaque binary and
   * no output format can be set on the Pipeline through the
   * Pipeline.Destination.output_payload_format field. Any Mediations on the
   * Pipeline that involve access to the data field will fail as persistent
   * errors.
   *
   * @param GoogleCloudEventarcV1PipelineMessagePayloadFormat $inputPayloadFormat
   */
  public function setInputPayloadFormat(GoogleCloudEventarcV1PipelineMessagePayloadFormat $inputPayloadFormat)
  {
    $this->inputPayloadFormat = $inputPayloadFormat;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMessagePayloadFormat
   */
  public function getInputPayloadFormat()
  {
    return $this->inputPayloadFormat;
  }
  /**
   * Optional. User labels attached to the Pipeline that can be used to group
   * resources. An object containing a list of "key": value pairs. Example: {
   * "name": "wrench", "mass": "1.3kg", "count": "3" }.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Config to control Platform Logging for Pipelines.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * Optional. List of mediation operations to be performed on the message.
   * Currently, only one Transformation operation is allowed in each Pipeline.
   *
   * @param GoogleCloudEventarcV1PipelineMediation[] $mediations
   */
  public function setMediations($mediations)
  {
    $this->mediations = $mediations;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMediation[]
   */
  public function getMediations()
  {
    return $this->mediations;
  }
  /**
   * Identifier. The resource name of the Pipeline. Must be unique within the
   * location of the project and must be in
   * `projects/{project}/locations/{location}/pipelines/{pipeline}` format.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The retry policy to use in the pipeline.
   *
   * @param GoogleCloudEventarcV1PipelineRetryPolicy $retryPolicy
   */
  public function setRetryPolicy(GoogleCloudEventarcV1PipelineRetryPolicy $retryPolicy)
  {
    $this->retryPolicy = $retryPolicy;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineRetryPolicy
   */
  public function getRetryPolicy()
  {
    return $this->retryPolicy;
  }
  /**
   * Output only. Whether or not this Pipeline satisfies the requirements of
   * physical zone separation
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Server-assigned unique identifier for the Pipeline. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The last-modified time. A timestamp in RFC3339 UTC "Zulu"
   * format, with nanosecond resolution and up to nine fractional digits.
   * Examples: "2014-10-02T15:01:23Z" and "2014-10-02T15:01:23.045123456Z".
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pipeline::class, 'Google_Service_Eventarc_Pipeline');
