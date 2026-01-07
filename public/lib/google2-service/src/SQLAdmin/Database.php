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

namespace Google\Service\SQLAdmin;

class Database extends \Google\Model
{
  /**
   * The Cloud SQL charset value.
   *
   * @var string
   */
  public $charset;
  /**
   * The Cloud SQL collation value.
   *
   * @var string
   */
  public $collation;
  /**
   * This field is deprecated and will be removed from a future version of the
   * API.
   *
   * @var string
   */
  public $etag;
  /**
   * The name of the Cloud SQL instance. This does not include the project ID.
   *
   * @var string
   */
  public $instance;
  /**
   * This is always `sql#database`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the database in the Cloud SQL instance. This does not include
   * the project ID or instance name.
   *
   * @var string
   */
  public $name;
  /**
   * The project ID of the project containing the Cloud SQL database. The Google
   * apps domain is prefixed if applicable.
   *
   * @var string
   */
  public $project;
  /**
   * The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  protected $sqlserverDatabaseDetailsType = SqlServerDatabaseDetails::class;
  protected $sqlserverDatabaseDetailsDataType = '';

  /**
   * The Cloud SQL charset value.
   *
   * @param string $charset
   */
  public function setCharset($charset)
  {
    $this->charset = $charset;
  }
  /**
   * @return string
   */
  public function getCharset()
  {
    return $this->charset;
  }
  /**
   * The Cloud SQL collation value.
   *
   * @param string $collation
   */
  public function setCollation($collation)
  {
    $this->collation = $collation;
  }
  /**
   * @return string
   */
  public function getCollation()
  {
    return $this->collation;
  }
  /**
   * This field is deprecated and will be removed from a future version of the
   * API.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The name of the Cloud SQL instance. This does not include the project ID.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * This is always `sql#database`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of the database in the Cloud SQL instance. This does not include
   * the project ID or instance name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The project ID of the project containing the Cloud SQL database. The Google
   * apps domain is prefixed if applicable.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The URI of this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * @param SqlServerDatabaseDetails $sqlserverDatabaseDetails
   */
  public function setSqlserverDatabaseDetails(SqlServerDatabaseDetails $sqlserverDatabaseDetails)
  {
    $this->sqlserverDatabaseDetails = $sqlserverDatabaseDetails;
  }
  /**
   * @return SqlServerDatabaseDetails
   */
  public function getSqlserverDatabaseDetails()
  {
    return $this->sqlserverDatabaseDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Database::class, 'Google_Service_SQLAdmin_Database');
