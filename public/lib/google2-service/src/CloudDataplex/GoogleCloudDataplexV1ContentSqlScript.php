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

class GoogleCloudDataplexV1ContentSqlScript extends \Google\Model
{
  /**
   * Value was unspecified.
   */
  public const ENGINE_QUERY_ENGINE_UNSPECIFIED = 'QUERY_ENGINE_UNSPECIFIED';
  /**
   * Spark SQL Query.
   */
  public const ENGINE_SPARK = 'SPARK';
  /**
   * Required. Query Engine to be used for the Sql Query.
   *
   * @var string
   */
  public $engine;

  /**
   * Required. Query Engine to be used for the Sql Query.
   *
   * Accepted values: QUERY_ENGINE_UNSPECIFIED, SPARK
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ContentSqlScript::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ContentSqlScript');
