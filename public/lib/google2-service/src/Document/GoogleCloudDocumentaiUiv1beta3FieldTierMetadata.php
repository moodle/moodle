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

namespace Google\Service\Document;

class GoogleCloudDocumentaiUiv1beta3FieldTierMetadata extends \Google\Model
{
  /**
   * Integer that indicates the tier of a property. e.g. Invoice has entities
   * that are classified as tier 1 which is the most important, while tier 2 and
   * tier 3 less so. This attribute can be used to filter schema attributes
   * before running eval. e.g. compute F1 score for only tier 1 entities. If not
   * present this attribute should be inferred as 1.
   *
   * @var int
   */
  public $tierLevel;

  /**
   * Integer that indicates the tier of a property. e.g. Invoice has entities
   * that are classified as tier 1 which is the most important, while tier 2 and
   * tier 3 less so. This attribute can be used to filter schema attributes
   * before running eval. e.g. compute F1 score for only tier 1 entities. If not
   * present this attribute should be inferred as 1.
   *
   * @param int $tierLevel
   */
  public function setTierLevel($tierLevel)
  {
    $this->tierLevel = $tierLevel;
  }
  /**
   * @return int
   */
  public function getTierLevel()
  {
    return $this->tierLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3FieldTierMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3FieldTierMetadata');
