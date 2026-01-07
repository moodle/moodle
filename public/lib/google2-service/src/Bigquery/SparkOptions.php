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

namespace Google\Service\Bigquery;

class SparkOptions extends \Google\Collection
{
  protected $collection_key = 'pyFileUris';
  /**
   * Archive files to be extracted into the working directory of each executor.
   * For more information about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @var string[]
   */
  public $archiveUris;
  /**
   * Fully qualified name of the user-provided Spark connection object. Format:
   * ```"projects/{project_id}/locations/{location_id}/connections/{connection_i
   * d}"```
   *
   * @var string
   */
  public $connection;
  /**
   * Custom container image for the runtime environment.
   *
   * @var string
   */
  public $containerImage;
  /**
   * Files to be placed in the working directory of each executor. For more
   * information about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @var string[]
   */
  public $fileUris;
  /**
   * JARs to include on the driver and executor CLASSPATH. For more information
   * about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @var string[]
   */
  public $jarUris;
  /**
   * The fully qualified name of a class in jar_uris, for example,
   * com.example.wordcount. Exactly one of main_class and main_jar_uri field
   * should be set for Java/Scala language type.
   *
   * @var string
   */
  public $mainClass;
  /**
   * The main file/jar URI of the Spark application. Exactly one of the
   * definition_body field and the main_file_uri field must be set for Python.
   * Exactly one of main_class and main_file_uri field should be set for
   * Java/Scala language type.
   *
   * @var string
   */
  public $mainFileUri;
  /**
   * Configuration properties as a set of key/value pairs, which will be passed
   * on to the Spark application. For more information, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html) and the [procedure
   * option list](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#procedure_option_list).
   *
   * @var string[]
   */
  public $properties;
  /**
   * Python files to be placed on the PYTHONPATH for PySpark application.
   * Supported file types: `.py`, `.egg`, and `.zip`. For more information about
   * Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @var string[]
   */
  public $pyFileUris;
  /**
   * Runtime version. If not specified, the default runtime version is used.
   *
   * @var string
   */
  public $runtimeVersion;

  /**
   * Archive files to be extracted into the working directory of each executor.
   * For more information about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
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
   * Fully qualified name of the user-provided Spark connection object. Format:
   * ```"projects/{project_id}/locations/{location_id}/connections/{connection_i
   * d}"```
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Custom container image for the runtime environment.
   *
   * @param string $containerImage
   */
  public function setContainerImage($containerImage)
  {
    $this->containerImage = $containerImage;
  }
  /**
   * @return string
   */
  public function getContainerImage()
  {
    return $this->containerImage;
  }
  /**
   * Files to be placed in the working directory of each executor. For more
   * information about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
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
   * JARs to include on the driver and executor CLASSPATH. For more information
   * about Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @param string[] $jarUris
   */
  public function setJarUris($jarUris)
  {
    $this->jarUris = $jarUris;
  }
  /**
   * @return string[]
   */
  public function getJarUris()
  {
    return $this->jarUris;
  }
  /**
   * The fully qualified name of a class in jar_uris, for example,
   * com.example.wordcount. Exactly one of main_class and main_jar_uri field
   * should be set for Java/Scala language type.
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
   * The main file/jar URI of the Spark application. Exactly one of the
   * definition_body field and the main_file_uri field must be set for Python.
   * Exactly one of main_class and main_file_uri field should be set for
   * Java/Scala language type.
   *
   * @param string $mainFileUri
   */
  public function setMainFileUri($mainFileUri)
  {
    $this->mainFileUri = $mainFileUri;
  }
  /**
   * @return string
   */
  public function getMainFileUri()
  {
    return $this->mainFileUri;
  }
  /**
   * Configuration properties as a set of key/value pairs, which will be passed
   * on to the Spark application. For more information, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html) and the [procedure
   * option list](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#procedure_option_list).
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
   * Python files to be placed on the PYTHONPATH for PySpark application.
   * Supported file types: `.py`, `.egg`, and `.zip`. For more information about
   * Apache Spark, see [Apache
   * Spark](https://spark.apache.org/docs/latest/index.html).
   *
   * @param string[] $pyFileUris
   */
  public function setPyFileUris($pyFileUris)
  {
    $this->pyFileUris = $pyFileUris;
  }
  /**
   * @return string[]
   */
  public function getPyFileUris()
  {
    return $this->pyFileUris;
  }
  /**
   * Runtime version. If not specified, the default runtime version is used.
   *
   * @param string $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkOptions::class, 'Google_Service_Bigquery_SparkOptions');
