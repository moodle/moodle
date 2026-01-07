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

class HadoopJob extends \Google\Collection
{
  protected $collection_key = 'jarFileUris';
  /**
   * Optional. HCFS URIs of archives to be extracted in the working directory of
   * Hadoop drivers and tasks. Supported file types: .jar, .tar, .tar.gz, .tgz,
   * or .zip.
   *
   * @var string[]
   */
  public $archiveUris;
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as -libjars or -Dfoo=bar, that can be set as job properties, since a
   * collision might occur that causes an incorrect job submission.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. HCFS (Hadoop Compatible Filesystem) URIs of files to be copied to
   * the working directory of Hadoop drivers and distributed tasks. Useful for
   * naively parallel tasks.
   *
   * @var string[]
   */
  public $fileUris;
  /**
   * Optional. Jar file URIs to add to the CLASSPATHs of the Hadoop driver and
   * tasks.
   *
   * @var string[]
   */
  public $jarFileUris;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * The name of the driver's main class. The jar file containing the class must
   * be in the default CLASSPATH or specified in jar_file_uris.
   *
   * @var string
   */
  public $mainClass;
  /**
   * The HCFS URI of the jar file containing the main class. Examples:
   * 'gs://foo-bucket/analytics-binaries/extract-useful-metrics-mr.jar'
   * 'hdfs:/tmp/test-samples/custom-wordcount.jar' 'file:home/usr/lib/hadoop-
   * mapreduce/hadoop-mapreduce-examples.jar'
   *
   * @var string
   */
  public $mainJarFileUri;
  /**
   * Optional. A mapping of property names to values, used to configure Hadoop.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/hadoop/conf-site and
   * classes in user code.
   *
   * @var string[]
   */
  public $properties;

  /**
   * Optional. HCFS URIs of archives to be extracted in the working directory of
   * Hadoop drivers and tasks. Supported file types: .jar, .tar, .tar.gz, .tgz,
   * or .zip.
   *
   * @param string[] $archiveUris
   */
  public function setArchiveUris($archiveUris)
  {
    $this->archiveUris = $archiveUris;
  }
  /**
   * @return string[]
   */
  public function getArchiveUris()
  {
    return $this->archiveUris;
  }
  /**
   * Optional. The arguments to pass to the driver. Do not include arguments,
   * such as -libjars or -Dfoo=bar, that can be set as job properties, since a
   * collision might occur that causes an incorrect job submission.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. HCFS (Hadoop Compatible Filesystem) URIs of files to be copied to
   * the working directory of Hadoop drivers and distributed tasks. Useful for
   * naively parallel tasks.
   *
   * @param string[] $fileUris
   */
  public function setFileUris($fileUris)
  {
    $this->fileUris = $fileUris;
  }
  /**
   * @return string[]
   */
  public function getFileUris()
  {
    return $this->fileUris;
  }
  /**
   * Optional. Jar file URIs to add to the CLASSPATHs of the Hadoop driver and
   * tasks.
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
   * The name of the driver's main class. The jar file containing the class must
   * be in the default CLASSPATH or specified in jar_file_uris.
   *
   * @param string $mainClass
   */
  public function setMainClass($mainClass)
  {
    $this->mainClass = $mainClass;
  }
  /**
   * @return string
   */
  public function getMainClass()
  {
    return $this->mainClass;
  }
  /**
   * The HCFS URI of the jar file containing the main class. Examples:
   * 'gs://foo-bucket/analytics-binaries/extract-useful-metrics-mr.jar'
   * 'hdfs:/tmp/test-samples/custom-wordcount.jar' 'file:home/usr/lib/hadoop-
   * mapreduce/hadoop-mapreduce-examples.jar'
   *
   * @param string $mainJarFileUri
   */
  public function setMainJarFileUri($mainJarFileUri)
  {
    $this->mainJarFileUri = $mainJarFileUri;
  }
  /**
   * @return string
   */
  public function getMainJarFileUri()
  {
    return $this->mainJarFileUri;
  }
  /**
   * Optional. A mapping of property names to values, used to configure Hadoop.
   * Properties that conflict with values set by the Dataproc API might be
   * overwritten. Can include properties set in /etc/hadoop/conf-site and
   * classes in user code.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HadoopJob::class, 'Google_Service_Dataproc_HadoopJob');
