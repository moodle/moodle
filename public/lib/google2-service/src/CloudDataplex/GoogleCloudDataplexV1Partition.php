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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Partition extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * Optional. The etag for this partition.
   *
   * @deprecated
   * @var string
   */
  public $etag;
  /**
   * Required. Immutable. The location of the entity data within the partition,
   * for example, gs://bucket/path/to/entity/key1=value1/key2=value2. Or
   * projects//datasets//tables/
   *
   * @var string
   */
  public $location;
  /**
   * Output only. Partition values used in the HTTP URL must be double encoded.
   * For example, url_encode(url_encode(value)) can be used to encode
   * "US:CA/CA#Sunnyvale so that the request URL ends with
   * "/partitions/US%253ACA/CA%2523Sunnyvale". The name field in the response
   * retains the encoded format.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The set of values representing the partition, which
   * correspond to the partition schema defined in the parent entity.
   *
   * @var string[]
   */
  public $values;

  /**
   * Optional. The etag for this partition.
   *
   * @deprecated
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. Immutable. The location of the entity data within the partition,
   * for example, gs://bucket/path/to/entity/key1=value1/key2=value2. Or
   * projects//datasets//tables/
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. Partition values used in the HTTP URL must be double encoded.
   * For example, url_encode(url_encode(value)) can be used to encode
   * "US:CA/CA#Sunnyvale so that the request URL ends with
   * "/partitions/US%253ACA/CA%2523Sunnyvale". The name field in the response
   * retains the encoded format.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Immutable. The set of values representing the partition, which
   * correspond to the partition schema defined in the parent entity.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1Partition::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Partition');
