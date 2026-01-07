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

class IssuerToUserInfo extends \Google\Model
{
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  public const ACTION_S2AP = 'S2AP';
  /**
   * Legacy alias for `S2AP`. Deprecated.
   *
   * @deprecated
   */
  public const ACTION_s2ap = 's2ap';
  public const ACTION_SIGN_UP = 'SIGN_UP';
  /**
   * Legacy alias for `SIGN_UP`. Deprecated.
   *
   * @deprecated
   */
  public const ACTION_signUp = 'signUp';
  /**
   * @var string
   */
  public $action;
  protected $signUpInfoType = SignUpInfo::class;
  protected $signUpInfoDataType = '';
  /**
   * Currently not used, consider deprecating.
   *
   * @var string
   */
  public $url;
  /**
   * JSON web token for action S2AP.
   *
   * @var string
   */
  public $value;

  /**
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * @param SignUpInfo $signUpInfo
   */
  public function setSignUpInfo(SignUpInfo $signUpInfo)
  {
    $this->signUpInfo = $signUpInfo;
  }
  /**
   * @return SignUpInfo
   */
  public function getSignUpInfo()
  {
    return $this->signUpInfo;
  }
  /**
   * Currently not used, consider deprecating.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * JSON web token for action S2AP.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IssuerToUserInfo::class, 'Google_Service_Walletobjects_IssuerToUserInfo');
