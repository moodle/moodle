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

namespace Google\Service\AnalyticsHub;

class DataProvider extends \Google\Model
{
  /**
   * Optional. Name of the data provider.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Email or URL of the data provider. Max Length: 1000 bytes.
   *
   * @var string
   */
  public $primaryContact;

  /**
   * Optional. Name of the data provider.
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
   * Optional. Email or URL of the data provider. Max Length: 1000 bytes.
   *
   * @param string $primaryContact
   */
  public function setPrimaryContact($primaryContact)
  {
    $this->primaryContact = $primaryContact;
  }
  /**
   * @return string
   */
  public function getPrimaryContact()
  {
    return $this->primaryContact;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataProvider::class, 'Google_Service_AnalyticsHub_DataProvider');
