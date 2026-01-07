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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1TaxonomyService extends \Google\Model
{
  /**
   * Default value
   */
  public const NAME_MANAGING_SYSTEM_UNSPECIFIED = 'MANAGING_SYSTEM_UNSPECIFIED';
  /**
   * Dataplex Universal Catalog.
   */
  public const NAME_MANAGING_SYSTEM_DATAPLEX = 'MANAGING_SYSTEM_DATAPLEX';
  /**
   * Other
   */
  public const NAME_MANAGING_SYSTEM_OTHER = 'MANAGING_SYSTEM_OTHER';
  /**
   * The service agent for the service.
   *
   * @var string
   */
  public $identity;
  /**
   * The Google Cloud service name.
   *
   * @var string
   */
  public $name;

  /**
   * The service agent for the service.
   *
   * @param string $identity
   */
  public function setIdentity($identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return string
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * The Google Cloud service name.
   *
   * Accepted values: MANAGING_SYSTEM_UNSPECIFIED, MANAGING_SYSTEM_DATAPLEX,
   * MANAGING_SYSTEM_OTHER
   *
   * @param self::NAME_* $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return self::NAME_*
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TaxonomyService::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TaxonomyService');
