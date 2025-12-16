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

namespace Google\Service\Walletobjects;

class JwtInsertResponse extends \Google\Model
{
  protected $resourcesType = Resources::class;
  protected $resourcesDataType = '';
  /**
   * A URI that, when opened, will allow the end user to save the object(s)
   * identified in the JWT to their Google account.
   *
   * @var string
   */
  public $saveUri;

  /**
   * Data that corresponds to the ids of the provided classes and objects in the
   * JWT. resources will only include the non-empty arrays (i.e. if the JWT only
   * includes eventTicketObjects, then that is the only field that will be
   * present in resources).
   *
   * @param Resources $resources
   */
  public function setResources(Resources $resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return Resources
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * A URI that, when opened, will allow the end user to save the object(s)
   * identified in the JWT to their Google account.
   *
   * @param string $saveUri
   */
  public function setSaveUri($saveUri)
  {
    $this->saveUri = $saveUri;
  }
  /**
   * @return string
   */
  public function getSaveUri()
  {
    return $this->saveUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JwtInsertResponse::class, 'Google_Service_Walletobjects_JwtInsertResponse');
