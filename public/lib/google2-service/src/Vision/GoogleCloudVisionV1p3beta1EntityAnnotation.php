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

class GoogleCloudVisionV1p3beta1EntityAnnotation extends \Google\Collection
{
  protected $collection_key = 'properties';
  protected $boundingPolyType = GoogleCloudVisionV1p3beta1BoundingPoly::class;
  protected $boundingPolyDataType = '';
  /**
   * **Deprecated. Use `score` instead.** The accuracy of the entity detection
   * in an image. For example, for an image in which the "Eiffel Tower" entity
   * is detected, this field represents the confidence that there is a tower in
   * the query image. Range [0, 1].
   *
   * @deprecated
   * @var float
   */
  public $confidence;
  /**
   * Entity textual description, expressed in its `locale` language.
   *
   * @var string
   */
  public $description;
  /**
   * The language code for the locale in which the entity textual `description`
   * is expressed.
   *
   * @var string
   */
  public $locale;
  protected $locationsType = GoogleCloudVisionV1p3beta1LocationInfo::class;
  protected $locationsDataType = 'array';
  /**
   * Opaque entity ID. Some IDs may be available in [Google Knowledge Graph
   * Search API](https://developers.google.com/knowledge-graph/).
   *
   * @var string
   */
  public $mid;
  protected $propertiesType = GoogleCloudVisionV1p3beta1Property::class;
  protected $propertiesDataType = 'array';
  /**
   * Overall score of the result. Range [0, 1].
   *
   * @var float
   */
  public $score;
  /**
   * The relevancy of the ICA (Image Content Annotation) label to the image. For
   * example, the relevancy of "tower" is likely higher to an image containing
   * the detected "Eiffel Tower" than to an image containing a detected distant
   * towering building, even though the confidence that there is a tower in each
   * image may be the same. Range [0, 1].
   *
   * @var float
   */
  public $topicality;

  /**
   * Image region to which this entity belongs. Not produced for
   * `LABEL_DETECTION` features.
   *
   * @param GoogleCloudVisionV1p3beta1BoundingPoly $boundingPoly
   */
  public function setBoundingPoly(GoogleCloudVisionV1p3beta1BoundingPoly $boundingPoly)
  {
    $this->boundingPoly = $boundingPoly;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1BoundingPoly
   */
  public function getBoundingPoly()
  {
    return $this->boundingPoly;
  }
  /**
   * **Deprecated. Use `score` instead.** The accuracy of the entity detection
   * in an image. For example, for an image in which the "Eiffel Tower" entity
   * is detected, this field represents the confidence that there is a tower in
   * the query image. Range [0, 1].
   *
   * @deprecated
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @deprecated
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Entity textual description, expressed in its `locale` language.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The language code for the locale in which the entity textual `description`
   * is expressed.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * The location information for the detected entity. Multiple `LocationInfo`
   * elements can be present because one location may indicate the location of
   * the scene in the image, and another location may indicate the location of
   * the place where the image was taken. Location information is usually
   * present for landmarks.
   *
   * @param GoogleCloudVisionV1p3beta1LocationInfo[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1LocationInfo[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Opaque entity ID. Some IDs may be available in [Google Knowledge Graph
   * Search API](https://developers.google.com/knowledge-graph/).
   *
   * @param string $mid
   */
  public function setMid($mid)
  {
    $this->mid = $mid;
  }
  /**
   * @return string
   */
  public function getMid()
  {
    return $this->mid;
  }
  /**
   * Some entities may have optional user-supplied `Property` (name/value)
   * fields, such a score or string that qualifies the entity.
   *
   * @param GoogleCloudVisionV1p3beta1Property[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudVisionV1p3beta1Property[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Overall score of the result. Range [0, 1].
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The relevancy of the ICA (Image Content Annotation) label to the image. For
   * example, the relevancy of "tower" is likely higher to an image containing
   * the detected "Eiffel Tower" than to an image containing a detected distant
   * towering building, even though the confidence that there is a tower in each
   * image may be the same. Range [0, 1].
   *
   * @param float $topicality
   */
  public function setTopicality($topicality)
  {
    $this->topicality = $topicality;
  }
  /**
   * @return float
   */
  public function getTopicality()
  {
    return $this->topicality;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p3beta1EntityAnnotation::class, 'Google_Service_Vision_GoogleCloudVisionV1p3beta1EntityAnnotation');
