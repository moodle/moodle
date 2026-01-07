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

namespace Google\Service\CloudIAP;

class CorsSettings extends \Google\Model
{
  /**
   * Configuration to allow HTTP `OPTIONS` calls to skip authentication and
   * authorization. If undefined, IAP will not apply any special logic to
   * `OPTIONS` requests.
   *
   * @var bool
   */
  public $allowHttpOptions;

  /**
   * Configuration to allow HTTP `OPTIONS` calls to skip authentication and
   * authorization. If undefined, IAP will not apply any special logic to
   * `OPTIONS` requests.
   *
   * @param bool $allowHttpOptions
   */
  public function setAllowHttpOptions($allowHttpOptions)
  {
    $this->allowHttpOptions = $allowHttpOptions;
  }
  /**
   * @return bool
   */
  public function getAllowHttpOptions()
  {
    return $this->allowHttpOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CorsSettings::class, 'Google_Service_CloudIAP_CorsSettings');
