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

namespace Google\Service\CloudSearch;

class AuditLoggingSettings extends \Google\Model
{
  /**
   * Indicates whether audit logging is on/off for admin activity read APIs i.e.
   * Get/List DataSources, Get/List SearchApplications etc.
   *
   * @var bool
   */
  public $logAdminReadActions;
  /**
   * Indicates whether audit logging is on/off for data access read APIs i.e.
   * ListItems, GetItem etc.
   *
   * @var bool
   */
  public $logDataReadActions;
  /**
   * Indicates whether audit logging is on/off for data access write APIs i.e.
   * IndexItem etc.
   *
   * @var bool
   */
  public $logDataWriteActions;
  /**
   * The resource name of the GCP Project to store audit logs. Cloud audit
   * logging will be enabled after project_name has been updated through
   * CustomerService. Format: projects/{project_id}
   *
   * @var string
   */
  public $project;

  /**
   * Indicates whether audit logging is on/off for admin activity read APIs i.e.
   * Get/List DataSources, Get/List SearchApplications etc.
   *
   * @param bool $logAdminReadActions
   */
  public function setLogAdminReadActions($logAdminReadActions)
  {
    $this->logAdminReadActions = $logAdminReadActions;
  }
  /**
   * @return bool
   */
  public function getLogAdminReadActions()
  {
    return $this->logAdminReadActions;
  }
  /**
   * Indicates whether audit logging is on/off for data access read APIs i.e.
   * ListItems, GetItem etc.
   *
   * @param bool $logDataReadActions
   */
  public function setLogDataReadActions($logDataReadActions)
  {
    $this->logDataReadActions = $logDataReadActions;
  }
  /**
   * @return bool
   */
  public function getLogDataReadActions()
  {
    return $this->logDataReadActions;
  }
  /**
   * Indicates whether audit logging is on/off for data access write APIs i.e.
   * IndexItem etc.
   *
   * @param bool $logDataWriteActions
   */
  public function setLogDataWriteActions($logDataWriteActions)
  {
    $this->logDataWriteActions = $logDataWriteActions;
  }
  /**
   * @return bool
   */
  public function getLogDataWriteActions()
  {
    return $this->logDataWriteActions;
  }
  /**
   * The resource name of the GCP Project to store audit logs. Cloud audit
   * logging will be enabled after project_name has been updated through
   * CustomerService. Format: projects/{project_id}
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuditLoggingSettings::class, 'Google_Service_CloudSearch_AuditLoggingSettings');
