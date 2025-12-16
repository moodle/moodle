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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1AnnotateVideoRequest extends \Google\Collection
{
  protected $collection_key = 'features';
  /**
   * Required. Requested video annotation features.
   *
   * @var string[]
   */
  public $features;
  /**
   * The video data bytes. If unset, the input video(s) should be specified via
   * the `input_uri`. If set, `input_uri` must be unset.
   *
   * @var string
   */
  public $inputContent;
  /**
   * Input video location. Currently, only [Cloud
   * Storage](https://cloud.google.com/storage/) URIs are supported. URIs must
   * be specified in the following format: `gs://bucket-id/object-id` (other URI
   * formats return google.rpc.Code.INVALID_ARGUMENT). For more information, see
   * [Request URIs](https://cloud.google.com/storage/docs/request-endpoints). To
   * identify multiple videos, a video URI may include wildcards in the `object-
   * id`. Supported wildcards: '*' to match 0 or more characters; '?' to match 1
   * character. If unset, the input video should be embedded in the request as
   * `input_content`. If set, `input_content` must be unset.
   *
   * @var string
   */
  public $inputUri;
  /**
   * Optional. Cloud region where annotation should take place. Supported cloud
   * regions are: `us-east1`, `us-west1`, `europe-west1`, `asia-east1`. If no
   * region is specified, the region will be determined based on video file
   * location.
   *
   * @var string
   */
  public $locationId;
  /**
   * Optional. Location where the output (in JSON format) should be stored.
   * Currently, only [Cloud Storage](https://cloud.google.com/storage/) URIs are
   * supported. These must be specified in the following format: `gs://bucket-
   * id/object-id` (other URI formats return google.rpc.Code.INVALID_ARGUMENT).
   * For more information, see [Request
   * URIs](https://cloud.google.com/storage/docs/request-endpoints).
   *
   * @var string
   */
  public $outputUri;
  protected $videoContextType = GoogleCloudVideointelligenceV1VideoContext::class;
  protected $videoContextDataType = '';

  /**
   * Required. Requested video annotation features.
   *
   * @param string[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return string[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * The video data bytes. If unset, the input video(s) should be specified via
   * the `input_uri`. If set, `input_uri` must be unset.
   *
   * @param string $inputContent
   */
  public function setInputContent($inputContent)
  {
    $this->inputContent = $inputContent;
  }
  /**
   * @return string
   */
  public function getInputContent()
  {
    return $this->inputContent;
  }
  /**
   * Input video location. Currently, only [Cloud
   * Storage](https://cloud.google.com/storage/) URIs are supported. URIs must
   * be specified in the following format: `gs://bucket-id/object-id` (other URI
   * formats return google.rpc.Code.INVALID_ARGUMENT). For more information, see
   * [Request URIs](https://cloud.google.com/storage/docs/request-endpoints). To
   * identify multiple videos, a video URI may include wildcards in the `object-
   * id`. Supported wildcards: '*' to match 0 or more characters; '?' to match 1
   * character. If unset, the input video should be embedded in the request as
   * `input_content`. If set, `input_content` must be unset.
   *
   * @param string $inputUri
   */
  public function setInputUri($inputUri)
  {
    $this->inputUri = $inputUri;
  }
  /**
   * @return string
   */
  public function getInputUri()
  {
    return $this->inputUri;
  }
  /**
   * Optional. Cloud region where annotation should take place. Supported cloud
   * regions are: `us-east1`, `us-west1`, `europe-west1`, `asia-east1`. If no
   * region is specified, the region will be determined based on video file
   * location.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Optional. Location where the output (in JSON format) should be stored.
   * Currently, only [Cloud Storage](https://cloud.google.com/storage/) URIs are
   * supported. These must be specified in the following format: `gs://bucket-
   * id/object-id` (other URI formats return google.rpc.Code.INVALID_ARGUMENT).
   * For more information, see [Request
   * URIs](https://cloud.google.com/storage/docs/request-endpoints).
   *
   * @param string $outputUri
   */
  public function setOutputUri($outputUri)
  {
    $this->outputUri = $outputUri;
  }
  /**
   * @return string
   */
  public function getOutputUri()
  {
    return $this->outputUri;
  }
  /**
   * Additional video context and/or feature-specific parameters.
   *
   * @param GoogleCloudVideointelligenceV1VideoContext $videoContext
   */
  public function setVideoContext(GoogleCloudVideointelligenceV1VideoContext $videoContext)
  {
    $this->videoContext = $videoContext;
  }
  /**
   * @return GoogleCloudVideointelligenceV1VideoContext
   */
  public function getVideoContext()
  {
    return $this->videoContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1AnnotateVideoRequest::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1AnnotateVideoRequest');
