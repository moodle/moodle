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

namespace Google\Service\CloudSearch;

class RetrievalImportance extends \Google\Model
{
  /**
   * Treat the match like a body text match.
   */
  public const IMPORTANCE_DEFAULT = 'DEFAULT';
  /**
   * Treat the match like a match against title of the item.
   */
  public const IMPORTANCE_HIGHEST = 'HIGHEST';
  /**
   * Treat the match with higher importance than body text.
   */
  public const IMPORTANCE_HIGH = 'HIGH';
  /**
   * Treat the match with lower importance than body text.
   */
  public const IMPORTANCE_LOW = 'LOW';
  /**
   * Do not match against this field during retrieval. The property can still be
   * used for operator matching, faceting, and suggest if desired.
   */
  public const IMPORTANCE_NONE = 'NONE';
  /**
   * Indicates the ranking importance given to property when it is matched
   * during retrieval. Once set, the token importance of a property cannot be
   * changed.
   *
   * @var string
   */
  public $importance;

  /**
   * Indicates the ranking importance given to property when it is matched
   * during retrieval. Once set, the token importance of a property cannot be
   * changed.
   *
   * Accepted values: DEFAULT, HIGHEST, HIGH, LOW, NONE
   *
   * @param self::IMPORTANCE_* $importance
   */
  public function setImportance($importance)
  {
    $this->importance = $importance;
  }
  /**
   * @return self::IMPORTANCE_*
   */
  public function getImportance()
  {
    return $this->importance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetrievalImportance::class, 'Google_Service_CloudSearch_RetrievalImportance');
