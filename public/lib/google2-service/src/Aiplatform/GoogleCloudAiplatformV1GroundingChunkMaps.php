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

class GoogleCloudAiplatformV1GroundingChunkMaps extends \Google\Model
{
  protected $placeAnswerSourcesType = GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources::class;
  protected $placeAnswerSourcesDataType = '';
  /**
   * This Place's resource name, in `places/{place_id}` format. This can be used
   * to look up the place in the Google Maps API.
   *
   * @var string
   */
  public $placeId;
  /**
   * The text of the place answer.
   *
   * @var string
   */
  public $text;
  /**
   * The title of the place.
   *
   * @var string
   */
  public $title;
  /**
   * The URI of the place.
   *
   * @var string
   */
  public $uri;

  /**
   * The sources that were used to generate the place answer. This includes
   * review snippets and photos that were used to generate the answer, as well
   * as URIs to flag content.
   *
   * @param GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources $placeAnswerSources
   */
  public function setPlaceAnswerSources(GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources $placeAnswerSources)
  {
    $this->placeAnswerSources = $placeAnswerSources;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSources
   */
  public function getPlaceAnswerSources()
  {
    return $this->placeAnswerSources;
  }
  /**
   * This Place's resource name, in `places/{place_id}` format. This can be used
   * to look up the place in the Google Maps API.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
  /**
   * The text of the place answer.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The title of the place.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The URI of the place.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingChunkMaps::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunkMaps');
