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

namespace Google\Service\AndroidManagement;

class DefaultApplicationContext extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const DEFAULT_APPLICATION_SCOPE_DEFAULT_APPLICATION_SCOPE_UNSPECIFIED = 'DEFAULT_APPLICATION_SCOPE_UNSPECIFIED';
  /**
   * Sets the application as the default on fully managed devices.
   */
  public const DEFAULT_APPLICATION_SCOPE_SCOPE_FULLY_MANAGED = 'SCOPE_FULLY_MANAGED';
  /**
   * Sets the application as the work profile default.Only supported for
   * DEFAULT_BROWSER, DEFAULT_CALL_REDIRECTION, DEFAULT_CALL_SCREENING,
   * DEFAULT_DIALER and DEFAULT_WALLET.
   */
  public const DEFAULT_APPLICATION_SCOPE_SCOPE_WORK_PROFILE = 'SCOPE_WORK_PROFILE';
  /**
   * Sets the application as the personal profile default on company-owned
   * devices with a work profile. Only pre-installed system apps can be set as
   * the default.Only supported for DEFAULT_BROWSER, DEFAULT_DIALER, DEFAULT_SMS
   * and DEFAULT_WALLET.
   */
  public const DEFAULT_APPLICATION_SCOPE_SCOPE_PERSONAL_PROFILE = 'SCOPE_PERSONAL_PROFILE';
  /**
   * Output only. The scope of non-compliant default application setting.
   *
   * @var string
   */
  public $defaultApplicationScope;

  /**
   * Output only. The scope of non-compliant default application setting.
   *
   * Accepted values: DEFAULT_APPLICATION_SCOPE_UNSPECIFIED,
   * SCOPE_FULLY_MANAGED, SCOPE_WORK_PROFILE, SCOPE_PERSONAL_PROFILE
   *
   * @param self::DEFAULT_APPLICATION_SCOPE_* $defaultApplicationScope
   */
  public function setDefaultApplicationScope($defaultApplicationScope)
  {
    $this->defaultApplicationScope = $defaultApplicationScope;
  }
  /**
   * @return self::DEFAULT_APPLICATION_SCOPE_*
   */
  public function getDefaultApplicationScope()
  {
    return $this->defaultApplicationScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultApplicationContext::class, 'Google_Service_AndroidManagement_DefaultApplicationContext');
