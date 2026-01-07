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

namespace Google\Service\Aiplatform;

class CloudAiLargeModelsVisionGenerateVideoResponse extends \Google\Collection
{
  protected $collection_key = 'videos';
  protected $generatedSamplesType = CloudAiLargeModelsVisionMedia::class;
  protected $generatedSamplesDataType = 'array';
  /**
   * Returns if any videos were filtered due to RAI policies.
   *
   * @var int
   */
  public $raiMediaFilteredCount;
  /**
   * Returns rai failure reasons if any.
   *
   * @var string[]
   */
  public $raiMediaFilteredReasons;
  protected $videosType = CloudAiLargeModelsVisionGenerateVideoResponseVideo::class;
  protected $videosDataType = 'array';

  /**
   * The generates samples.
   *
   * @param CloudAiLargeModelsVisionMedia[] $generatedSamples
   */
  public function setGeneratedSamples($generatedSamples)
  {
    $this->generatedSamples = $generatedSamples;
  }
  /**
   * @return CloudAiLargeModelsVisionMedia[]
   */
  public function getGeneratedSamples()
  {
    return $this->generatedSamples;
  }
  /**
   * Returns if any videos were filtered due to RAI policies.
   *
   * @param int $raiMediaFilteredCount
   */
  public function setRaiMediaFilteredCount($raiMediaFilteredCount)
  {
    $this->raiMediaFilteredCount = $raiMediaFilteredCount;
  }
  /**
   * @return int
   */
  public function getRaiMediaFilteredCount()
  {
    return $this->raiMediaFilteredCount;
  }
  /**
   * Returns rai failure reasons if any.
   *
   * @param string[] $raiMediaFilteredReasons
   */
  public function setRaiMediaFilteredReasons($raiMediaFilteredReasons)
  {
    $this->raiMediaFilteredReasons = $raiMediaFilteredReasons;
  }
  /**
   * @return string[]
   */
  public function getRaiMediaFilteredReasons()
  {
    return $this->raiMediaFilteredReasons;
  }
  /**
   * List of videos, used to align naming with the external response.
   *
   * @param CloudAiLargeModelsVisionGenerateVideoResponseVideo[] $videos
   */
  public function setVideos($videos)
  {
    $this->videos = $videos;
  }
  /**
   * @return CloudAiLargeModelsVisionGenerateVideoResponseVideo[]
   */
  public function getVideos()
  {
    return $this->videos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiLargeModelsVisionGenerateVideoResponse::class, 'Google_Service_Aiplatform_CloudAiLargeModelsVisionGenerateVideoResponse');
