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

class PigJob extends \Google\Collection
{
  protected $collection_key = 'jarFileUris';
  /**
   * Optional. Whether to continue executing queries if a query fails. The
   * default value is false. Setting to true can be useful when executing
   * independent parallel queries.
   *
   * @var bool
   */
  public $continueOnFailure;
  /**
   * Optional. HCFS URIs of jar files to add to the CLASSPATH of the Pig Client
   * and Hadoop MapReduce (MR) tasks. Can contain Pig UDFs.
   *
   * @var string[]
   */
  public $jarFileUris;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Optional. A mapping of property names to values, used to configure Pig.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/hadoop/conf-site.xml,
   * /etc/pig/conf/pig.properties, and classes in user code.
   *
   * @var string[]
   */
  public $properties;
  /**
   * The HCFS URI of the script that contains the Pig queries.
   *
   * @var string
   */
  public $queryFileUri;
  protected $queryListType = QueryList::class;
  protected $queryListDataType = '';
  /**
   * Optional. Mapping of query variable names to values (equivalent to the Pig
   * command: name=[value]).
   *
   * @var string[]
   */
  public $scriptVariables;

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
   * Optional. HCFS URIs of jar files to add to the CLASSPATH of the Pig Client
   * and Hadoop MapReduce (MR) tasks. Can contain Pig UDFs.
   *
   * @param string[] $jarFileUris
   */
  public function setJarFileUris($jarFileUris)
  {
    $this->jarFileUris = $jarFileUris;
  }
  /**
   * @return string[]
   */
  public function getJarFileUris()
  {
    return $this->jarFileUris;
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
   * Optional. A mapping of property names to values, used to configure Pig.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/hadoop/conf-site.xml,
   * /etc/pig/conf/pig.properties, and classes in user code.
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
   * The HCFS URI of the script that contains the Pig queries.
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
  /**
   * Optional. Mapping of query variable names to values (equivalent to the Pig
   * command: name=[value]).
   *
   * @param string[] $scriptVariables
   */
  public function setScriptVariables($scriptVariables)
  {
    $this->scriptVariables = $scriptVariables;
  }
  /**
   * @return string[]
   */
  public function getScriptVariables()
  {
    return $this->scriptVariables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PigJob::class, 'Google_Service_Dataproc_PigJob');
