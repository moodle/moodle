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

namespace Google\Service\Dataproc;

class PrestoJob extends \Google\Collection
{
  protected $collection_key = 'clientTags';
  /**
   * Optional. Presto client tags to attach to this query
   *
   * @var string[]
   */
  public $clientTags;
  /**
   * Optional. Whether to continue executing queries if a query fails. The
   * default value is false. Setting to true can be useful when executing
   * independent parallel queries.
   *
   * @var bool
   */
  public $continueOnFailure;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Optional. The format in which query output will be displayed. See the
   * Presto documentation for supported output formats
   *
   * @var string
   */
  public $outputFormat;
  /**
   * Optional. A mapping of property names to values. Used to set Presto session
   * properties (https://prestodb.io/docs/current/sql/set-session.html)
   * Equivalent to using the --session flag in the Presto CLI
   *
   * @var string[]
   */
  public $properties;
  /**
   * The HCFS URI of the script that contains SQL queries.
   *
   * @var string
   */
  public $queryFileUri;
  protected $queryListType = QueryList::class;
  protected $queryListDataType = '';

  /**
   * Optional. Presto client tags to attach to this query
   *
   * @param string[] $clientTags
   */
  public function setClientTags($clientTags)
  {
    $this->clientTags = $clientTags;
  }
  /**
   * @return string[]
   */
  public function getClientTags()
  {
    return $this->clientTags;
  }
  /**
   * Optional. Whether to continue executing queries if a query fails. The
   * default value is false. Setting to true can be useful when executing
   * independent parallel queries.
   *
   * @param bool $continueOnFailure
   */
  public function setContinueOnFailure($continueOnFailure)
  {
    $this->continueOnFailure = $continueOnFailure;
  }
  /**
   * @return bool
   */
  public function getContinueOnFailure()
  {
    return $this->continueOnFailure;
  }
  /**
   * Optional. The runtime log config for job execution.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * Optional. The format in which query output will be displayed. See the
   * Presto documentation for supported output formats
   *
   * @param string $outputFormat
   */
  public function setOutputFormat($outputFormat)
  {
    $this->outputFormat = $outputFormat;
  }
  /**
   * @return string
   */
  public function getOutputFormat()
  {
    return $this->outputFormat;
  }
  /**
   * Optional. A mapping of property names to values. Used to set Presto session
   * properties (https://prestodb.io/docs/current/sql/set-session.html)
   * Equivalent to using the --session flag in the Presto CLI
   *
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The HCFS URI of the script that contains SQL queries.
   *
   * @param string $queryFileUri
   */
  public function setQueryFileUri($queryFileUri)
  {
    $this->queryFileUri = $queryFileUri;
  }
  /**
   * @return string
   */
  public function getQueryFileUri()
  {
    return $this->queryFileUri;
  }
  /**
   * A list of queries.
   *
   * @param QueryList $queryList
   */
  public function setQueryList(QueryList $queryList)
  {
    $this->queryList = $queryList;
  }
  /**
   * @return QueryList
   */
  public function getQueryList()
  {
    return $this->queryList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrestoJob::class, 'Google_Service_Dataproc_PrestoJob');
