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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AliasRevisionConfig extends \Google\Model
{
  /**
   * Alias type is not specified.
   */
  public const TYPE_ALIAS_TYPE_UNSPECIFIED = 'ALIAS_TYPE_UNSPECIFIED';
  /**
   * Certificate.
   */
  public const TYPE_CERT = 'CERT';
  /**
   * Key/certificate pair.
   */
  public const TYPE_KEY_CERT = 'KEY_CERT';
  /**
   * Location of the alias file. For example, a Google Cloud Storage URI.
   *
   * @var string
   */
  public $location;
  /**
   * Name of the alias revision included in the keystore in the following
   * format: `organizations/{org}/environments/{env}/keystores/{keystore}/aliase
   * s/{alias}/revisions/{rev}`
   *
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $type;

  /**
   * Location of the alias file. For example, a Google Cloud Storage URI.
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
   * Name of the alias revision included in the keystore in the following
   * format: `organizations/{org}/environments/{env}/keystores/{keystore}/aliase
   * s/{alias}/revisions/{rev}`
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
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AliasRevisionConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AliasRevisionConfig');
