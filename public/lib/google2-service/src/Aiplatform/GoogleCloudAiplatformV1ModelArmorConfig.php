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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ModelArmorConfig extends \Google\Model
{
  /**
   * Optional. The resource name of the Model Armor template to use for prompt
   * screening. A Model Armor template is a set of customized filters and
   * thresholds that define how Model Armor screens content. If specified, Model
   * Armor will use this template to check the user's prompt for safety and
   * security risks before it is sent to the model. The name must be in the
   * format `projects/{project}/locations/{location}/templates/{template}`.
   *
   * @var string
   */
  public $promptTemplateName;
  /**
   * Optional. The resource name of the Model Armor template to use for response
   * screening. A Model Armor template is a set of customized filters and
   * thresholds that define how Model Armor screens content. If specified, Model
   * Armor will use this template to check the model's response for safety and
   * security risks before it is returned to the user. The name must be in the
   * format `projects/{project}/locations/{location}/templates/{template}`.
   *
   * @var string
   */
  public $responseTemplateName;

  /**
   * Optional. The resource name of the Model Armor template to use for prompt
   * screening. A Model Armor template is a set of customized filters and
   * thresholds that define how Model Armor screens content. If specified, Model
   * Armor will use this template to check the user's prompt for safety and
   * security risks before it is sent to the model. The name must be in the
   * format `projects/{project}/locations/{location}/templates/{template}`.
   *
   * @param string $promptTemplateName
   */
  public function setPromptTemplateName($promptTemplateName)
  {
    $this->promptTemplateName = $promptTemplateName;
  }
  /**
   * @return string
   */
  public function getPromptTemplateName()
  {
    return $this->promptTemplateName;
  }
  /**
   * Optional. The resource name of the Model Armor template to use for response
   * screening. A Model Armor template is a set of customized filters and
   * thresholds that define how Model Armor screens content. If specified, Model
   * Armor will use this template to check the model's response for safety and
   * security risks before it is returned to the user. The name must be in the
   * format `projects/{project}/locations/{location}/templates/{template}`.
   *
   * @param string $responseTemplateName
   */
  public function setResponseTemplateName($responseTemplateName)
  {
    $this->responseTemplateName = $responseTemplateName;
  }
  /**
   * @return string
   */
  public function getResponseTemplateName()
  {
    return $this->responseTemplateName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelArmorConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelArmorConfig');
