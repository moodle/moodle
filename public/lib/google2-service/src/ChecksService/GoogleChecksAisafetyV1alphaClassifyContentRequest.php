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

namespace Google\Service\ChecksService;

class GoogleChecksAisafetyV1alphaClassifyContentRequest extends \Google\Collection
{
  /**
   * Unspecified version.
   */
  public const CLASSIFIER_VERSION_CLASSIFIER_VERSION_UNSPECIFIED = 'CLASSIFIER_VERSION_UNSPECIFIED';
  /**
   * Stable version.
   */
  public const CLASSIFIER_VERSION_STABLE = 'STABLE';
  /**
   * Latest version.
   */
  public const CLASSIFIER_VERSION_LATEST = 'LATEST';
  protected $collection_key = 'policies';
  /**
   * Optional. Version of the classifier to use. If not specified, the latest
   * version will be used.
   *
   * @var string
   */
  public $classifierVersion;
  protected $contextType = GoogleChecksAisafetyV1alphaClassifyContentRequestContext::class;
  protected $contextDataType = '';
  protected $inputType = GoogleChecksAisafetyV1alphaClassifyContentRequestInputContent::class;
  protected $inputDataType = '';
  protected $policiesType = GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig::class;
  protected $policiesDataType = 'array';

  /**
   * Optional. Version of the classifier to use. If not specified, the latest
   * version will be used.
   *
   * Accepted values: CLASSIFIER_VERSION_UNSPECIFIED, STABLE, LATEST
   *
   * @param self::CLASSIFIER_VERSION_* $classifierVersion
   */
  public function setClassifierVersion($classifierVersion)
  {
    $this->classifierVersion = $classifierVersion;
  }
  /**
   * @return self::CLASSIFIER_VERSION_*
   */
  public function getClassifierVersion()
  {
    return $this->classifierVersion;
  }
  /**
   * Optional. Context about the input that will be used to help on the
   * classification.
   *
   * @param GoogleChecksAisafetyV1alphaClassifyContentRequestContext $context
   */
  public function setContext(GoogleChecksAisafetyV1alphaClassifyContentRequestContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return GoogleChecksAisafetyV1alphaClassifyContentRequestContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Required. Content to be classified.
   *
   * @param GoogleChecksAisafetyV1alphaClassifyContentRequestInputContent $input
   */
  public function setInput(GoogleChecksAisafetyV1alphaClassifyContentRequestInputContent $input)
  {
    $this->input = $input;
  }
  /**
   * @return GoogleChecksAisafetyV1alphaClassifyContentRequestInputContent
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * Required. List of policies to classify against.
   *
   * @param GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return GoogleChecksAisafetyV1alphaClassifyContentRequestPolicyConfig[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksAisafetyV1alphaClassifyContentRequest::class, 'Google_Service_ChecksService_GoogleChecksAisafetyV1alphaClassifyContentRequest');
