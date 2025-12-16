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

class GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation extends \Google\Model
{
  /**
   * Optional. The SQL expression.
   *
   * @var string
   */
  public $sqlExpression;

  /**
   * Optional. The SQL expression.
   *
   * @param string $sqlExpression
   */
  public function setSqlExpression($sqlExpression)
  {
    $this->sqlExpression = $sqlExpression;
  }
  /**
   * @return string
   */
  public function getSqlExpression()
  {
    return $this->sqlExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualityRuleTableConditionExpectation');
