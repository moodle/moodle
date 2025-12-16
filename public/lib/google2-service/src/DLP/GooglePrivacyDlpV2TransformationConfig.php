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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TransformationConfig extends \Google\Model
{
  /**
   * De-identify template. If this template is specified, it will serve as the
   * default de-identify template. This template cannot contain
   * `record_transformations` since it can be used for unstructured content such
   * as free-form text files. If this template is not set, a default
   * `ReplaceWithInfoTypeConfig` will be used to de-identify unstructured
   * content.
   *
   * @var string
   */
  public $deidentifyTemplate;
  /**
   * Image redact template. If this template is specified, it will serve as the
   * de-identify template for images. If this template is not set, all findings
   * in the image will be redacted with a black box.
   *
   * @var string
   */
  public $imageRedactTemplate;
  /**
   * Structured de-identify template. If this template is specified, it will
   * serve as the de-identify template for structured content such as delimited
   * files and tables. If this template is not set but the `deidentify_template`
   * is set, then `deidentify_template` will also apply to the structured
   * content. If neither template is set, a default `ReplaceWithInfoTypeConfig`
   * will be used to de-identify structured content.
   *
   * @var string
   */
  public $structuredDeidentifyTemplate;

  /**
   * De-identify template. If this template is specified, it will serve as the
   * default de-identify template. This template cannot contain
   * `record_transformations` since it can be used for unstructured content such
   * as free-form text files. If this template is not set, a default
   * `ReplaceWithInfoTypeConfig` will be used to de-identify unstructured
   * content.
   *
   * @param string $deidentifyTemplate
   */
  public function setDeidentifyTemplate($deidentifyTemplate)
  {
    $this->deidentifyTemplate = $deidentifyTemplate;
  }
  /**
   * @return string
   */
  public function getDeidentifyTemplate()
  {
    return $this->deidentifyTemplate;
  }
  /**
   * Image redact template. If this template is specified, it will serve as the
   * de-identify template for images. If this template is not set, all findings
   * in the image will be redacted with a black box.
   *
   * @param string $imageRedactTemplate
   */
  public function setImageRedactTemplate($imageRedactTemplate)
  {
    $this->imageRedactTemplate = $imageRedactTemplate;
  }
  /**
   * @return string
   */
  public function getImageRedactTemplate()
  {
    return $this->imageRedactTemplate;
  }
  /**
   * Structured de-identify template. If this template is specified, it will
   * serve as the de-identify template for structured content such as delimited
   * files and tables. If this template is not set but the `deidentify_template`
   * is set, then `deidentify_template` will also apply to the structured
   * content. If neither template is set, a default `ReplaceWithInfoTypeConfig`
   * will be used to de-identify structured content.
   *
   * @param string $structuredDeidentifyTemplate
   */
  public function setStructuredDeidentifyTemplate($structuredDeidentifyTemplate)
  {
    $this->structuredDeidentifyTemplate = $structuredDeidentifyTemplate;
  }
  /**
   * @return string
   */
  public function getStructuredDeidentifyTemplate()
  {
    return $this->structuredDeidentifyTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationConfig');
