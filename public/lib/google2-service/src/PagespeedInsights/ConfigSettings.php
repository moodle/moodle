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

namespace Google\Service\PagespeedInsights;

class ConfigSettings extends \Google\Model
{
  /**
   * How Lighthouse was run, e.g. from the Chrome extension or from the npm
   * module.
   *
   * @var string
   */
  public $channel;
  /**
   * The form factor the emulation should use. This field is deprecated,
   * form_factor should be used instead.
   *
   * @deprecated
   * @var string
   */
  public $emulatedFormFactor;
  /**
   * How Lighthouse should interpret this run in regards to scoring performance
   * metrics and skipping mobile-only tests in desktop.
   *
   * @var string
   */
  public $formFactor;
  /**
   * The locale setting.
   *
   * @var string
   */
  public $locale;
  /**
   * List of categories of audits the run should conduct.
   *
   * @var array
   */
  public $onlyCategories;

  /**
   * How Lighthouse was run, e.g. from the Chrome extension or from the npm
   * module.
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * The form factor the emulation should use. This field is deprecated,
   * form_factor should be used instead.
   *
   * @deprecated
   * @param string $emulatedFormFactor
   */
  public function setEmulatedFormFactor($emulatedFormFactor)
  {
    $this->emulatedFormFactor = $emulatedFormFactor;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEmulatedFormFactor()
  {
    return $this->emulatedFormFactor;
  }
  /**
   * How Lighthouse should interpret this run in regards to scoring performance
   * metrics and skipping mobile-only tests in desktop.
   *
   * @param string $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return string
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * The locale setting.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * List of categories of audits the run should conduct.
   *
   * @param array $onlyCategories
   */
  public function setOnlyCategories($onlyCategories)
  {
    $this->onlyCategories = $onlyCategories;
  }
  /**
   * @return array
   */
  public function getOnlyCategories()
  {
    return $this->onlyCategories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigSettings::class, 'Google_Service_PagespeedInsights_ConfigSettings');
