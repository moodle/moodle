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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1ExternalSystem extends \Google\Collection
{
  protected $collection_key = 'assignees';
  /**
   * References primary/secondary etc assignees in the external system.
   *
   * @var string[]
   */
  public $assignees;
  /**
   * The time when the case was closed, as reported by the external system.
   *
   * @var string
   */
  public $caseCloseTime;
  /**
   * The time when the case was created, as reported by the external system.
   *
   * @var string
   */
  public $caseCreateTime;
  /**
   * The priority of the finding's corresponding case in the external system.
   *
   * @var string
   */
  public $casePriority;
  /**
   * The SLA of the finding's corresponding case in the external system.
   *
   * @var string
   */
  public $caseSla;
  /**
   * The link to the finding's corresponding case in the external system.
   *
   * @var string
   */
  public $caseUri;
  /**
   * The time when the case was last updated, as reported by the external
   * system.
   *
   * @var string
   */
  public $externalSystemUpdateTime;
  /**
   * The identifier that's used to track the finding's corresponding case in the
   * external system.
   *
   * @var string
   */
  public $externalUid;
  /**
   * Full resource name of the external system, for example:
   * "organizations/1234/sources/5678/findings/123456/externalSystems/jira",
   * "folders/1234/sources/5678/findings/123456/externalSystems/jira",
   * "projects/1234/sources/5678/findings/123456/externalSystems/jira"
   *
   * @var string
   */
  public $name;
  /**
   * The most recent status of the finding's corresponding case, as reported by
   * the external system.
   *
   * @var string
   */
  public $status;
  protected $ticketInfoType = TicketInfo::class;
  protected $ticketInfoDataType = '';

  /**
   * References primary/secondary etc assignees in the external system.
   *
   * @param string[] $assignees
   */
  public function setAssignees($assignees)
  {
    $this->assignees = $assignees;
  }
  /**
   * @return string[]
   */
  public function getAssignees()
  {
    return $this->assignees;
  }
  /**
   * The time when the case was closed, as reported by the external system.
   *
   * @param string $caseCloseTime
   */
  public function setCaseCloseTime($caseCloseTime)
  {
    $this->caseCloseTime = $caseCloseTime;
  }
  /**
   * @return string
   */
  public function getCaseCloseTime()
  {
    return $this->caseCloseTime;
  }
  /**
   * The time when the case was created, as reported by the external system.
   *
   * @param string $caseCreateTime
   */
  public function setCaseCreateTime($caseCreateTime)
  {
    $this->caseCreateTime = $caseCreateTime;
  }
  /**
   * @return string
   */
  public function getCaseCreateTime()
  {
    return $this->caseCreateTime;
  }
  /**
   * The priority of the finding's corresponding case in the external system.
   *
   * @param string $casePriority
   */
  public function setCasePriority($casePriority)
  {
    $this->casePriority = $casePriority;
  }
  /**
   * @return string
   */
  public function getCasePriority()
  {
    return $this->casePriority;
  }
  /**
   * The SLA of the finding's corresponding case in the external system.
   *
   * @param string $caseSla
   */
  public function setCaseSla($caseSla)
  {
    $this->caseSla = $caseSla;
  }
  /**
   * @return string
   */
  public function getCaseSla()
  {
    return $this->caseSla;
  }
  /**
   * The link to the finding's corresponding case in the external system.
   *
   * @param string $caseUri
   */
  public function setCaseUri($caseUri)
  {
    $this->caseUri = $caseUri;
  }
  /**
   * @return string
   */
  public function getCaseUri()
  {
    return $this->caseUri;
  }
  /**
   * The time when the case was last updated, as reported by the external
   * system.
   *
   * @param string $externalSystemUpdateTime
   */
  public function setExternalSystemUpdateTime($externalSystemUpdateTime)
  {
    $this->externalSystemUpdateTime = $externalSystemUpdateTime;
  }
  /**
   * @return string
   */
  public function getExternalSystemUpdateTime()
  {
    return $this->externalSystemUpdateTime;
  }
  /**
   * The identifier that's used to track the finding's corresponding case in the
   * external system.
   *
   * @param string $externalUid
   */
  public function setExternalUid($externalUid)
  {
    $this->externalUid = $externalUid;
  }
  /**
   * @return string
   */
  public function getExternalUid()
  {
    return $this->externalUid;
  }
  /**
   * Full resource name of the external system, for example:
   * "organizations/1234/sources/5678/findings/123456/externalSystems/jira",
   * "folders/1234/sources/5678/findings/123456/externalSystems/jira",
   * "projects/1234/sources/5678/findings/123456/externalSystems/jira"
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
   * The most recent status of the finding's corresponding case, as reported by
   * the external system.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Information about the ticket, if any, that is being used to track the
   * resolution of the issue that is identified by this finding.
   *
   * @param TicketInfo $ticketInfo
   */
  public function setTicketInfo(TicketInfo $ticketInfo)
  {
    $this->ticketInfo = $ticketInfo;
  }
  /**
   * @return TicketInfo
   */
  public function getTicketInfo()
  {
    return $this->ticketInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1ExternalSystem::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1ExternalSystem');
