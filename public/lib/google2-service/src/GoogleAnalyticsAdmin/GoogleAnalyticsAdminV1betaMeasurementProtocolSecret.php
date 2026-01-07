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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaMeasurementProtocolSecret extends \Google\Model
{
  /**
   * Required. Human-readable display name for this secret.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of this secret. This secret may be a child of
   * any type of stream. Format: properties/{property}/dataStreams/{dataStream}/
   * measurementProtocolSecrets/{measurementProtocolSecret}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The measurement protocol secret value. Pass this value to the
   * api_secret field of the Measurement Protocol API when sending hits to this
   * secret's parent property.
   *
   * @var string
   */
  public $secretValue;

  /**
   * Required. Human-readable display name for this secret.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Resource name of this secret. This secret may be a child of
   * any type of stream. Format: properties/{property}/dataStreams/{dataStream}/
   * measurementProtocolSecrets/{measurementProtocolSecret}
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
   * Output only. The measurement protocol secret value. Pass this value to the
   * api_secret field of the Measurement Protocol API when sending hits to this
   * secret's parent property.
   *
   * @param string $secretValue
   */
  public function setSecretValue($secretValue)
  {
    $this->secretValue = $secretValue;
  }
  /**
   * @return string
   */
  public function getSecretValue()
  {
    return $this->secretValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaMeasurementProtocolSecret::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaMeasurementProtocolSecret');
