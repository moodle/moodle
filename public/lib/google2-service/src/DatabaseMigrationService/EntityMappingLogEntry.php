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

namespace Google\Service\DatabaseMigrationService;

class EntityMappingLogEntry extends \Google\Model
{
  /**
   * Comment.
   *
   * @var string
   */
  public $mappingComment;
  /**
   * Which rule caused this log entry.
   *
   * @var string
   */
  public $ruleId;
  /**
   * Rule revision ID.
   *
   * @var string
   */
  public $ruleRevisionId;

  /**
   * Comment.
   *
   * @param string $mappingComment
   */
  public function setMappingComment($mappingComment)
  {
    $this->mappingComment = $mappingComment;
  }
  /**
   * @return string
   */
  public function getMappingComment()
  {
    return $this->mappingComment;
  }
  /**
   * Which rule caused this log entry.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
  /**
   * Rule revision ID.
   *
   * @param string $ruleRevisionId
   */
  public function setRuleRevisionId($ruleRevisionId)
  {
    $this->ruleRevisionId = $ruleRevisionId;
  }
  /**
   * @return string
   */
  public function getRuleRevisionId()
  {
    return $this->ruleRevisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityMappingLogEntry::class, 'Google_Service_DatabaseMigrationService_EntityMappingLogEntry');
