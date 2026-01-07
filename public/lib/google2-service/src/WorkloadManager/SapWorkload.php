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

namespace Google\Service\WorkloadManager;

class SapWorkload extends \Google\Collection
{
  /**
   * Unspecified architecture.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * Invaliad architecture.
   */
  public const ARCHITECTURE_INVALID = 'INVALID';
  /**
   * A centralized system.
   */
  public const ARCHITECTURE_CENTRALIZED = 'CENTRALIZED';
  /**
   * A distributed system.
   */
  public const ARCHITECTURE_DISTRIBUTED = 'DISTRIBUTED';
  /**
   * A distributed with HA system.
   */
  public const ARCHITECTURE_DISTRIBUTED_HA = 'DISTRIBUTED_HA';
  /**
   * A standalone database system.
   */
  public const ARCHITECTURE_STANDALONE_DATABASE = 'STANDALONE_DATABASE';
  /**
   * A standalone database with HA system.
   */
  public const ARCHITECTURE_STANDALONE_DATABASE_HA = 'STANDALONE_DATABASE_HA';
  protected $collection_key = 'products';
  protected $applicationType = SapComponent::class;
  protected $applicationDataType = '';
  /**
   * Output only. the architecture
   *
   * @var string
   */
  public $architecture;
  protected $databaseType = SapComponent::class;
  protected $databaseDataType = '';
  /**
   * Output only. The metadata for SAP workload.
   *
   * @var string[]
   */
  public $metadata;
  protected $productsType = Product::class;
  protected $productsDataType = 'array';

  /**
   * Output only. the acsc componment
   *
   * @param SapComponent $application
   */
  public function setApplication(SapComponent $application)
  {
    $this->application = $application;
  }
  /**
   * @return SapComponent
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * Output only. the architecture
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, INVALID, CENTRALIZED,
   * DISTRIBUTED, DISTRIBUTED_HA, STANDALONE_DATABASE, STANDALONE_DATABASE_HA
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Output only. the database componment
   *
   * @param SapComponent $database
   */
  public function setDatabase(SapComponent $database)
  {
    $this->database = $database;
  }
  /**
   * @return SapComponent
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Output only. The metadata for SAP workload.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. the products on this workload.
   *
   * @param Product[] $products
   */
  public function setProducts($products)
  {
    $this->products = $products;
  }
  /**
   * @return Product[]
   */
  public function getProducts()
  {
    return $this->products;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapWorkload::class, 'Google_Service_WorkloadManager_SapWorkload');
