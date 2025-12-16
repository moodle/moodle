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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1ContentBlock extends \Google\Collection
{
  protected $collection_key = 'referencedPlaces';
  protected $contentType = GoogleTypeLocalizedText::class;
  protected $contentDataType = '';
  /**
   * The list of resource names of the referenced places. This name can be used
   * in other APIs that accept Place resource names.
   *
   * @var string[]
   */
  public $referencedPlaces;

  /**
   * Content related to the topic.
   *
   * @param GoogleTypeLocalizedText $content
   */
  public function setContent(GoogleTypeLocalizedText $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleTypeLocalizedText
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The list of resource names of the referenced places. This name can be used
   * in other APIs that accept Place resource names.
   *
   * @param string[] $referencedPlaces
   */
  public function setReferencedPlaces($referencedPlaces)
  {
    $this->referencedPlaces = $referencedPlaces;
  }
  /**
   * @return string[]
   */
  public function getReferencedPlaces()
  {
    return $this->referencedPlaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1ContentBlock::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContentBlock');
