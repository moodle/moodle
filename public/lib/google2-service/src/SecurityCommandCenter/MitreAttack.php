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

class MitreAttack extends \Google\Collection
{
  /**
   * Unspecified value.
   */
  public const PRIMARY_TACTIC_TACTIC_UNSPECIFIED = 'TACTIC_UNSPECIFIED';
  /**
   * TA0043
   */
  public const PRIMARY_TACTIC_RECONNAISSANCE = 'RECONNAISSANCE';
  /**
   * TA0042
   */
  public const PRIMARY_TACTIC_RESOURCE_DEVELOPMENT = 'RESOURCE_DEVELOPMENT';
  /**
   * TA0001
   */
  public const PRIMARY_TACTIC_INITIAL_ACCESS = 'INITIAL_ACCESS';
  /**
   * TA0002
   */
  public const PRIMARY_TACTIC_EXECUTION = 'EXECUTION';
  /**
   * TA0003
   */
  public const PRIMARY_TACTIC_PERSISTENCE = 'PERSISTENCE';
  /**
   * TA0004
   */
  public const PRIMARY_TACTIC_PRIVILEGE_ESCALATION = 'PRIVILEGE_ESCALATION';
  /**
   * TA0005
   */
  public const PRIMARY_TACTIC_DEFENSE_EVASION = 'DEFENSE_EVASION';
  /**
   * TA0006
   */
  public const PRIMARY_TACTIC_CREDENTIAL_ACCESS = 'CREDENTIAL_ACCESS';
  /**
   * TA0007
   */
  public const PRIMARY_TACTIC_DISCOVERY = 'DISCOVERY';
  /**
   * TA0008
   */
  public const PRIMARY_TACTIC_LATERAL_MOVEMENT = 'LATERAL_MOVEMENT';
  /**
   * TA0009
   */
  public const PRIMARY_TACTIC_COLLECTION = 'COLLECTION';
  /**
   * TA0011
   */
  public const PRIMARY_TACTIC_COMMAND_AND_CONTROL = 'COMMAND_AND_CONTROL';
  /**
   * TA0010
   */
  public const PRIMARY_TACTIC_EXFILTRATION = 'EXFILTRATION';
  /**
   * TA0040
   */
  public const PRIMARY_TACTIC_IMPACT = 'IMPACT';
  protected $collection_key = 'primaryTechniques';
  /**
   * Additional MITRE ATT&CK tactics related to this finding, if any.
   *
   * @var string[]
   */
  public $additionalTactics;
  /**
   * Additional MITRE ATT&CK techniques related to this finding, if any, along
   * with any of their respective parent techniques.
   *
   * @var string[]
   */
  public $additionalTechniques;
  /**
   * The MITRE ATT&CK tactic most closely represented by this finding, if any.
   *
   * @var string
   */
  public $primaryTactic;
  /**
   * The MITRE ATT&CK technique most closely represented by this finding, if
   * any. primary_techniques is a repeated field because there are multiple
   * levels of MITRE ATT&CK techniques. If the technique most closely
   * represented by this finding is a sub-technique (e.g. `SCANNING_IP_BLOCKS`),
   * both the sub-technique and its parent technique(s) will be listed (e.g.
   * `SCANNING_IP_BLOCKS`, `ACTIVE_SCANNING`).
   *
   * @var string[]
   */
  public $primaryTechniques;
  /**
   * The MITRE ATT&CK version referenced by the above fields. E.g. "8".
   *
   * @var string
   */
  public $version;

  /**
   * Additional MITRE ATT&CK tactics related to this finding, if any.
   *
   * @param string[] $additionalTactics
   */
  public function setAdditionalTactics($additionalTactics)
  {
    $this->additionalTactics = $additionalTactics;
  }
  /**
   * @return string[]
   */
  public function getAdditionalTactics()
  {
    return $this->additionalTactics;
  }
  /**
   * Additional MITRE ATT&CK techniques related to this finding, if any, along
   * with any of their respective parent techniques.
   *
   * @param string[] $additionalTechniques
   */
  public function setAdditionalTechniques($additionalTechniques)
  {
    $this->additionalTechniques = $additionalTechniques;
  }
  /**
   * @return string[]
   */
  public function getAdditionalTechniques()
  {
    return $this->additionalTechniques;
  }
  /**
   * The MITRE ATT&CK tactic most closely represented by this finding, if any.
   *
   * Accepted values: TACTIC_UNSPECIFIED, RECONNAISSANCE, RESOURCE_DEVELOPMENT,
   * INITIAL_ACCESS, EXECUTION, PERSISTENCE, PRIVILEGE_ESCALATION,
   * DEFENSE_EVASION, CREDENTIAL_ACCESS, DISCOVERY, LATERAL_MOVEMENT,
   * COLLECTION, COMMAND_AND_CONTROL, EXFILTRATION, IMPACT
   *
   * @param self::PRIMARY_TACTIC_* $primaryTactic
   */
  public function setPrimaryTactic($primaryTactic)
  {
    $this->primaryTactic = $primaryTactic;
  }
  /**
   * @return self::PRIMARY_TACTIC_*
   */
  public function getPrimaryTactic()
  {
    return $this->primaryTactic;
  }
  /**
   * The MITRE ATT&CK technique most closely represented by this finding, if
   * any. primary_techniques is a repeated field because there are multiple
   * levels of MITRE ATT&CK techniques. If the technique most closely
   * represented by this finding is a sub-technique (e.g. `SCANNING_IP_BLOCKS`),
   * both the sub-technique and its parent technique(s) will be listed (e.g.
   * `SCANNING_IP_BLOCKS`, `ACTIVE_SCANNING`).
   *
   * @param string[] $primaryTechniques
   */
  public function setPrimaryTechniques($primaryTechniques)
  {
    $this->primaryTechniques = $primaryTechniques;
  }
  /**
   * @return string[]
   */
  public function getPrimaryTechniques()
  {
    return $this->primaryTechniques;
  }
  /**
   * The MITRE ATT&CK version referenced by the above fields. E.g. "8".
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MitreAttack::class, 'Google_Service_SecurityCommandCenter_MitreAttack');
