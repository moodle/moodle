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

class ActivationOptions extends \Google\Model
{
  /**
   * HTTPS URL that supports REST semantics. Would be used for requesting
   * activation from partners for given valuable, triggered by the users.
   *
   * @var string
   */
  public $activationUrl;
  /**
   * Flag to allow users to make activation call from different device. This
   * allows client to render the activation button enabled even if the
   * activationStatus is ACTIVATED but the requested device is different than
   * the current device.
   *
   * @var bool
   */
  public $allowReactivation;

  /**
   * HTTPS URL that supports REST semantics. Would be used for requesting
   * activation from partners for given valuable, triggered by the users.
   *
   * @param string $activationUrl
   */
  public function setActivationUrl($activationUrl)
  {
    $this->activationUrl = $activationUrl;
  }
  /**
   * @return string
   */
  public function getActivationUrl()
  {
    return $this->activationUrl;
  }
  /**
   * Flag to allow users to make activation call from different device. This
   * allows client to render the activation button enabled even if the
   * activationStatus is ACTIVATED but the requested device is different than
   * the current device.
   *
   * @param bool $allowReactivation
   */
  public function setAllowReactivation($allowReactivation)
  {
    $this->allowReactivation = $allowReactivation;
  }
  /**
   * @return bool
   */
  public function getAllowReactivation()
  {
    return $this->allowReactivation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivationOptions::class, 'Google_Service_Walletobjects_ActivationOptions');
