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

namespace Google\Service\ServiceUsage;

class ContentSecurityProvider extends \Google\Model
{
  /**
   * Name of security service for content scanning, such as Google Cloud Model
   * Armor or supported third-party ISV solutions. If it is Google 1P service,
   * the name should be prefixed with `services/`. If it is a 3P service, the
   * format needs to be documented. The currently supported values are: -
   * `services/modelarmor.googleapis.com` for Google Cloud Model Armor.
   *
   * @var string
   */
  public $name;

  /**
   * Name of security service for content scanning, such as Google Cloud Model
   * Armor or supported third-party ISV solutions. If it is Google 1P service,
   * the name should be prefixed with `services/`. If it is a 3P service, the
   * format needs to be documented. The currently supported values are: -
   * `services/modelarmor.googleapis.com` for Google Cloud Model Armor.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentSecurityProvider::class, 'Google_Service_ServiceUsage_ContentSecurityProvider');
