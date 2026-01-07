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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1GenerateDataQualityRulesResponse extends \Google\Collection
{
  protected $collection_key = 'rule';
  protected $ruleType = GoogleCloudDataplexV1DataQualityRule::class;
  protected $ruleDataType = 'array';

  /**
   * The data quality rules that Dataplex Universal Catalog generates based on
   * the results of a data profiling scan.
   *
   * @param GoogleCloudDataplexV1DataQualityRule[] $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRule[]
   */
  public function getRule()
  {
    return $this->rule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1GenerateDataQualityRulesResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1GenerateDataQualityRulesResponse');
