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

class GoogleCloudVideointelligenceV1p1beta1Entity extends \Google\Model
{
  /**
   * Textual description, e.g., `Fixed-gear bicycle`.
   *
   * @var string
   */
  public $description;
  /**
   * Opaque entity ID. Some IDs may be available in [Google Knowledge Graph
   * Search API](https://developers.google.com/knowledge-graph/).
   *
   * @var string
   */
  public $entityId;
  /**
   * Language code for `description` in BCP-47 format.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Textual description, e.g., `Fixed-gear bicycle`.
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
   * Opaque entity ID. Some IDs may be available in [Google Knowledge Graph
   * Search API](https://developers.google.com/knowledge-graph/).
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * Language code for `description` in BCP-47 format.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1p1beta1Entity::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1p1beta1Entity');
