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

namespace Google\Service\SecurityCommandCenter;

class Chokepoint extends \Google\Collection
{
  protected $collection_key = 'relatedFindings';
  /**
   * List of resource names of findings associated with this chokepoint. For
   * example, organizations/123/sources/456/findings/789. This list will have at
   * most 100 findings.
   *
   * @var string[]
   */
  public $relatedFindings;

  /**
   * List of resource names of findings associated with this chokepoint. For
   * example, organizations/123/sources/456/findings/789. This list will have at
   * most 100 findings.
   *
   * @param string[] $relatedFindings
   */
  public function setRelatedFindings($relatedFindings)
  {
    $this->relatedFindings = $relatedFindings;
  }
  /**
   * @return string[]
   */
  public function getRelatedFindings()
  {
    return $this->relatedFindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Chokepoint::class, 'Google_Service_SecurityCommandCenter_Chokepoint');
