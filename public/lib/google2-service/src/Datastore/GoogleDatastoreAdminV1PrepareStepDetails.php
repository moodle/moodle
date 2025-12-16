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

namespace Google\Service\Datastore;

class GoogleDatastoreAdminV1PrepareStepDetails extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const CONCURRENCY_MODE_CONCURRENCY_MODE_UNSPECIFIED = 'CONCURRENCY_MODE_UNSPECIFIED';
  /**
   * Pessimistic concurrency.
   */
  public const CONCURRENCY_MODE_PESSIMISTIC = 'PESSIMISTIC';
  /**
   * Optimistic concurrency.
   */
  public const CONCURRENCY_MODE_OPTIMISTIC = 'OPTIMISTIC';
  /**
   * Optimistic concurrency with entity groups.
   */
  public const CONCURRENCY_MODE_OPTIMISTIC_WITH_ENTITY_GROUPS = 'OPTIMISTIC_WITH_ENTITY_GROUPS';
  /**
   * The concurrency mode this database will use when it reaches the
   * `REDIRECT_WRITES` step.
   *
   * @var string
   */
  public $concurrencyMode;

  /**
   * The concurrency mode this database will use when it reaches the
   * `REDIRECT_WRITES` step.
   *
   * Accepted values: CONCURRENCY_MODE_UNSPECIFIED, PESSIMISTIC, OPTIMISTIC,
   * OPTIMISTIC_WITH_ENTITY_GROUPS
   *
   * @param self::CONCURRENCY_MODE_* $concurrencyMode
   */
  public function setConcurrencyMode($concurrencyMode)
  {
    $this->concurrencyMode = $concurrencyMode;
  }
  /**
   * @return self::CONCURRENCY_MODE_*
   */
  public function getConcurrencyMode()
  {
    return $this->concurrencyMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1PrepareStepDetails::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1PrepareStepDetails');
