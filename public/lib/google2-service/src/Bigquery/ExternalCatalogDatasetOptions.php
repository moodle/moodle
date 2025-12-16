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

namespace Google\Service\Bigquery;

class ExternalCatalogDatasetOptions extends \Google\Model
{
  /**
   * Optional. The storage location URI for all tables in the dataset.
   * Equivalent to hive metastore's database locationUri. Maximum length of 1024
   * characters.
   *
   * @var string
   */
  public $defaultStorageLocationUri;
  /**
   * Optional. A map of key value pairs defining the parameters and properties
   * of the open source schema. Maximum size of 2MiB.
   *
   * @var string[]
   */
  public $parameters;

  /**
   * Optional. The storage location URI for all tables in the dataset.
   * Equivalent to hive metastore's database locationUri. Maximum length of 1024
   * characters.
   *
   * @param string $defaultStorageLocationUri
   */
  public function setDefaultStorageLocationUri($defaultStorageLocationUri)
  {
    $this->defaultStorageLocationUri = $defaultStorageLocationUri;
  }
  /**
   * @return string
   */
  public function getDefaultStorageLocationUri()
  {
    return $this->defaultStorageLocationUri;
  }
  /**
   * Optional. A map of key value pairs defining the parameters and properties
   * of the open source schema. Maximum size of 2MiB.
   *
   * @param string[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalCatalogDatasetOptions::class, 'Google_Service_Bigquery_ExternalCatalogDatasetOptions');
