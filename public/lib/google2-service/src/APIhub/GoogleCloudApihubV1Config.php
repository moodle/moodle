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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Config extends \Google\Model
{
  /**
   * Encryption type unspecified.
   */
  public const ENCRYPTION_TYPE_ENCRYPTION_TYPE_UNSPECIFIED = 'ENCRYPTION_TYPE_UNSPECIFIED';
  /**
   * Default encryption using Google managed encryption key.
   */
  public const ENCRYPTION_TYPE_GMEK = 'GMEK';
  /**
   * Encryption using customer managed encryption key.
   */
  public const ENCRYPTION_TYPE_CMEK = 'CMEK';
  /**
   * Optional. The Customer Managed Encryption Key (CMEK) used for data
   * encryption. The CMEK name should follow the format of
   * `projects/([^/]+)/locations/([^/]+)/keyRings/([^/]+)/cryptoKeys/([^/]+)`,
   * where the location must match the instance location. If the CMEK is not
   * provided, a GMEK will be created for the instance.
   *
   * @var string
   */
  public $cmekKeyName;
  /**
   * Optional. If true, the search will be disabled for the instance. The
   * default value is false.
   *
   * @var bool
   */
  public $disableSearch;
  /**
   * Optional. Encryption type for the region. If the encryption type is CMEK,
   * the cmek_key_name must be provided. If no encryption type is provided, GMEK
   * will be used.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Optional. The name of the Vertex AI location where the data store is
   * stored.
   *
   * @var string
   */
  public $vertexLocation;

  /**
   * Optional. The Customer Managed Encryption Key (CMEK) used for data
   * encryption. The CMEK name should follow the format of
   * `projects/([^/]+)/locations/([^/]+)/keyRings/([^/]+)/cryptoKeys/([^/]+)`,
   * where the location must match the instance location. If the CMEK is not
   * provided, a GMEK will be created for the instance.
   *
   * @param string $cmekKeyName
   */
  public function setCmekKeyName($cmekKeyName)
  {
    $this->cmekKeyName = $cmekKeyName;
  }
  /**
   * @return string
   */
  public function getCmekKeyName()
  {
    return $this->cmekKeyName;
  }
  /**
   * Optional. If true, the search will be disabled for the instance. The
   * default value is false.
   *
   * @param bool $disableSearch
   */
  public function setDisableSearch($disableSearch)
  {
    $this->disableSearch = $disableSearch;
  }
  /**
   * @return bool
   */
  public function getDisableSearch()
  {
    return $this->disableSearch;
  }
  /**
   * Optional. Encryption type for the region. If the encryption type is CMEK,
   * the cmek_key_name must be provided. If no encryption type is provided, GMEK
   * will be used.
   *
   * Accepted values: ENCRYPTION_TYPE_UNSPECIFIED, GMEK, CMEK
   *
   * @param self::ENCRYPTION_TYPE_* $encryptionType
   */
  public function setEncryptionType($encryptionType)
  {
    $this->encryptionType = $encryptionType;
  }
  /**
   * @return self::ENCRYPTION_TYPE_*
   */
  public function getEncryptionType()
  {
    return $this->encryptionType;
  }
  /**
   * Optional. The name of the Vertex AI location where the data store is
   * stored.
   *
   * @param string $vertexLocation
   */
  public function setVertexLocation($vertexLocation)
  {
    $this->vertexLocation = $vertexLocation;
  }
  /**
   * @return string
   */
  public function getVertexLocation()
  {
    return $this->vertexLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Config::class, 'Google_Service_APIhub_GoogleCloudApihubV1Config');
