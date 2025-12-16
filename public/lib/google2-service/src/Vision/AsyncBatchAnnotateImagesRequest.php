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

namespace Google\Service\Vision;

class AsyncBatchAnnotateImagesRequest extends \Google\Collection
{
  protected $collection_key = 'requests';
  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter.
   *
   * @var string[]
   */
  public $labels;
  protected $outputConfigType = OutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Optional. Target project and location to make a call. Format:
   * `projects/{project-id}/locations/{location-id}`. If no parent is specified,
   * a region will be chosen automatically. Supported location-ids: `us`: USA
   * country only, `asia`: East asia areas, like Japan, Taiwan, `eu`: The
   * European Union. Example: `projects/project-A/locations/eu`.
   *
   * @var string
   */
  public $parent;
  protected $requestsType = AnnotateImageRequest::class;
  protected $requestsDataType = 'array';

  /**
   * Optional. The labels with user-defined metadata for the request. Label keys
   * and values can be no longer than 63 characters (Unicode codepoints), can
   * only contain lowercase letters, numeric characters, underscores and dashes.
   * International characters are allowed. Label values are optional. Label keys
   * must start with a letter.
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
   * Required. The desired output location and metadata (e.g. format).
   *
   * @param OutputConfig $outputConfig
   */
  public function setOutputConfig(OutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return OutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Optional. Target project and location to make a call. Format:
   * `projects/{project-id}/locations/{location-id}`. If no parent is specified,
   * a region will be chosen automatically. Supported location-ids: `us`: USA
   * country only, `asia`: East asia areas, like Japan, Taiwan, `eu`: The
   * European Union. Example: `projects/project-A/locations/eu`.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. Individual image annotation requests for this batch.
   *
   * @param AnnotateImageRequest[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return AnnotateImageRequest[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AsyncBatchAnnotateImagesRequest::class, 'Google_Service_Vision_AsyncBatchAnnotateImagesRequest');
