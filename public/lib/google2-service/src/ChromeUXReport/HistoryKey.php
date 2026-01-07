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

namespace Google\Service\ChromeUXReport;

class HistoryKey extends \Google\Model
{
  /**
   * The default value, representing all device classes.
   */
  public const FORM_FACTOR_ALL_FORM_FACTORS = 'ALL_FORM_FACTORS';
  /**
   * The device class representing a "mobile"/"phone" sized client.
   */
  public const FORM_FACTOR_PHONE = 'PHONE';
  /**
   * The device class representing a "desktop"/"laptop" type full size client.
   */
  public const FORM_FACTOR_DESKTOP = 'DESKTOP';
  /**
   * The device class representing a "tablet" type client.
   */
  public const FORM_FACTOR_TABLET = 'TABLET';
  /**
   * The form factor is the device class that all users used to access the site
   * for this record. If the form factor is unspecified, then aggregated data
   * over all form factors will be returned.
   *
   * @var string
   */
  public $formFactor;
  /**
   * Origin specifies the origin that this record is for. Note: When specifying
   * an origin, data for loads under this origin over all pages are aggregated
   * into origin level user experience data.
   *
   * @var string
   */
  public $origin;
  /**
   * Url specifies a specific url that this record is for. This url should be
   * normalized, following the normalization actions taken in the request to
   * increase the chances of successful lookup. Note: When specifying a "url"
   * only data for that specific url will be aggregated.
   *
   * @var string
   */
  public $url;

  /**
   * The form factor is the device class that all users used to access the site
   * for this record. If the form factor is unspecified, then aggregated data
   * over all form factors will be returned.
   *
   * Accepted values: ALL_FORM_FACTORS, PHONE, DESKTOP, TABLET
   *
   * @param self::FORM_FACTOR_* $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return self::FORM_FACTOR_*
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * Origin specifies the origin that this record is for. Note: When specifying
   * an origin, data for loads under this origin over all pages are aggregated
   * into origin level user experience data.
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Url specifies a specific url that this record is for. This url should be
   * normalized, following the normalization actions taken in the request to
   * increase the chances of successful lookup. Note: When specifying a "url"
   * only data for that specific url will be aggregated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistoryKey::class, 'Google_Service_ChromeUXReport_HistoryKey');
