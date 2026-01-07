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

namespace Google\Service\Compute;

class InterconnectGroupPhysicalStructure extends \Google\Collection
{
  protected $collection_key = 'metros';
  protected $metrosType = InterconnectGroupPhysicalStructureMetros::class;
  protected $metrosDataType = 'array';

  /**
   * @param InterconnectGroupPhysicalStructureMetros[] $metros
   */
  public function setMetros($metros)
  {
    $this->metros = $metros;
  }
  /**
   * @return InterconnectGroupPhysicalStructureMetros[]
   */
  public function getMetros()
  {
    return $this->metros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupPhysicalStructure::class, 'Google_Service_Compute_InterconnectGroupPhysicalStructure');
