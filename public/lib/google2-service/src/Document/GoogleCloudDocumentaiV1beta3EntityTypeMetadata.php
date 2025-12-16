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

class GoogleCloudDocumentaiV1beta3EntityTypeMetadata extends \Google\Model
{
  /**
   * Whether the entity type should be considered inactive.
   *
   * @var bool
   */
  public $inactive;

  /**
   * Whether the entity type should be considered inactive.
   *
   * @param bool $inactive
   */
  public function setInactive($inactive)
  {
    $this->inactive = $inactive;
  }
  /**
   * @return bool
   */
  public function getInactive()
  {
    return $this->inactive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3EntityTypeMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3EntityTypeMetadata');
