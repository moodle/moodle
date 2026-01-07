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

namespace Google\Service\Adsense;

class Alert extends \Google\Model
{
  /**
   * Unspecified severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Info.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Warning.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Severe.
   */
  public const SEVERITY_SEVERE = 'SEVERE';
  /**
   * Output only. The localized alert message. This may contain HTML markup,
   * such as phrase elements or links.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. Resource name of the alert. Format:
   * accounts/{account}/alerts/{alert}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Severity of this alert.
   *
   * @var string
   */
  public $severity;
  /**
   * Output only. Type of alert. This identifies the broad type of this alert,
   * and provides a stable machine-readable identifier that will not be
   * translated. For example, "payment-hold".
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The localized alert message. This may contain HTML markup,
   * such as phrase elements or links.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. Resource name of the alert. Format:
   * accounts/{account}/alerts/{alert}
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
   * Output only. Severity of this alert.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFO, WARNING, SEVERE
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Output only. Type of alert. This identifies the broad type of this alert,
   * and provides a stable machine-readable identifier that will not be
   * translated. For example, "payment-hold".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Alert::class, 'Google_Service_Adsense_Alert');
