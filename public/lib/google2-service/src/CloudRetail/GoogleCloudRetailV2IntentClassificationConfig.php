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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2IntentClassificationConfig extends \Google\Collection
{
  protected $collection_key = 'example';
  /**
   * Optional. A list of keywords that will be used to classify the query to the
   * "BLOCKLISTED" intent type. The keywords are case insensitive.
   *
   * @var string[]
   */
  public $blocklistKeywords;
  /**
   * Optional. A list of intent types that will be disabled for this customer.
   * The intent types must match one of the predefined intent types defined at h
   * ttps://cloud.google.com/retail/docs/reference/rpc/google.cloud.retail.v2alp
   * ha#querytype
   *
   * @var string[]
   */
  public $disabledIntentTypes;
  protected $exampleType = GoogleCloudRetailV2IntentClassificationConfigExample::class;
  protected $exampleDataType = 'array';
  protected $inlineSourceType = GoogleCloudRetailV2IntentClassificationConfigInlineSource::class;
  protected $inlineSourceDataType = '';
  /**
   * Optional. Customers can use the preamble to specify any requirements for
   * blocklisting intent classification. This preamble will be added to the
   * blocklisting intent classification model prompt.
   *
   * @var string
   */
  public $modelPreamble;

  /**
   * Optional. A list of keywords that will be used to classify the query to the
   * "BLOCKLISTED" intent type. The keywords are case insensitive.
   *
   * @param string[] $blocklistKeywords
   */
  public function setBlocklistKeywords($blocklistKeywords)
  {
    $this->blocklistKeywords = $blocklistKeywords;
  }
  /**
   * @return string[]
   */
  public function getBlocklistKeywords()
  {
    return $this->blocklistKeywords;
  }
  /**
   * Optional. A list of intent types that will be disabled for this customer.
   * The intent types must match one of the predefined intent types defined at h
   * ttps://cloud.google.com/retail/docs/reference/rpc/google.cloud.retail.v2alp
   * ha#querytype
   *
   * @param string[] $disabledIntentTypes
   */
  public function setDisabledIntentTypes($disabledIntentTypes)
  {
    $this->disabledIntentTypes = $disabledIntentTypes;
  }
  /**
   * @return string[]
   */
  public function getDisabledIntentTypes()
  {
    return $this->disabledIntentTypes;
  }
  /**
   * Optional. A list of examples for intent classification.
   *
   * @param GoogleCloudRetailV2IntentClassificationConfigExample[] $example
   */
  public function setExample($example)
  {
    $this->example = $example;
  }
  /**
   * @return GoogleCloudRetailV2IntentClassificationConfigExample[]
   */
  public function getExample()
  {
    return $this->example;
  }
  /**
   * Optional. Inline source for intent classifications.
   *
   * @param GoogleCloudRetailV2IntentClassificationConfigInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudRetailV2IntentClassificationConfigInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudRetailV2IntentClassificationConfigInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
  /**
   * Optional. Customers can use the preamble to specify any requirements for
   * blocklisting intent classification. This preamble will be added to the
   * blocklisting intent classification model prompt.
   *
   * @param string $modelPreamble
   */
  public function setModelPreamble($modelPreamble)
  {
    $this->modelPreamble = $modelPreamble;
  }
  /**
   * @return string
   */
  public function getModelPreamble()
  {
    return $this->modelPreamble;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2IntentClassificationConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2IntentClassificationConfig');
