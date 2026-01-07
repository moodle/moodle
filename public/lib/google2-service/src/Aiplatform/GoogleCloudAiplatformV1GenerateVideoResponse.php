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

class GoogleCloudAiplatformV1GenerateVideoResponse extends \Google\Collection
{
  protected $collection_key = 'videos';
  /**
   * The cloud storage uris of the generated videos.
   *
   * @deprecated
   * @var string[]
   */
  public $generatedSamples;
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
  protected $videosType = GoogleCloudAiplatformV1GenerateVideoResponseVideo::class;
  protected $videosDataType = 'array';

  /**
   * The cloud storage uris of the generated videos.
   *
   * @deprecated
   * @param string[] $generatedSamples
   */
  public function setGeneratedSamples($generatedSamples)
  {
    $this->generatedSamples = $generatedSamples;
  }
  /**
   * @deprecated
   * @return string[]
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
   * List of video bytes or Cloud Storage URIs of the generated videos.
   *
   * @param GoogleCloudAiplatformV1GenerateVideoResponseVideo[] $videos
   */
  public function setVideos($videos)
  {
    $this->videos = $videos;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerateVideoResponseVideo[]
   */
  public function getVideos()
  {
    return $this->videos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateVideoResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateVideoResponse');
