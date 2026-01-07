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

namespace Google\Service\CloudHealthcare;

class EntityMention extends \Google\Collection
{
  protected $collection_key = 'linkedEntities';
  protected $certaintyAssessmentType = Feature::class;
  protected $certaintyAssessmentDataType = '';
  /**
   * The model's confidence in this entity mention annotation. A number between
   * 0 and 1.
   *
   * @var 
   */
  public $confidence;
  protected $linkedEntitiesType = LinkedEntity::class;
  protected $linkedEntitiesDataType = 'array';
  /**
   * mention_id uniquely identifies each entity mention in a single response.
   *
   * @var string
   */
  public $mentionId;
  protected $subjectType = Feature::class;
  protected $subjectDataType = '';
  protected $temporalAssessmentType = Feature::class;
  protected $temporalAssessmentDataType = '';
  protected $textType = TextSpan::class;
  protected $textDataType = '';
  /**
   * The semantic type of the entity: UNKNOWN_ENTITY_TYPE, ALONE,
   * ANATOMICAL_STRUCTURE, ASSISTED_LIVING, BF_RESULT, BM_RESULT, BM_UNIT,
   * BM_VALUE, BODY_FUNCTION, BODY_MEASUREMENT, COMPLIANT, DOESNOT_FOLLOWUP,
   * FAMILY, FOLLOWSUP, LABORATORY_DATA, LAB_RESULT, LAB_UNIT, LAB_VALUE,
   * MEDICAL_DEVICE, MEDICINE, MED_DOSE, MED_DURATION, MED_FORM, MED_FREQUENCY,
   * MED_ROUTE, MED_STATUS, MED_STRENGTH, MED_TOTALDOSE, MED_UNIT,
   * NON_COMPLIANT, OTHER_LIVINGSTATUS, PROBLEM, PROCEDURE, PROCEDURE_RESULT,
   * PROC_METHOD, REASON_FOR_NONCOMPLIANCE, SEVERITY, SUBSTANCE_ABUSE,
   * UNCLEAR_FOLLOWUP.
   *
   * @var string
   */
  public $type;

  /**
   * The certainty assessment of the entity mention. Its value is one of:
   * LIKELY, SOMEWHAT_LIKELY, UNCERTAIN, SOMEWHAT_UNLIKELY, UNLIKELY,
   * CONDITIONAL
   *
   * @param Feature $certaintyAssessment
   */
  public function setCertaintyAssessment(Feature $certaintyAssessment)
  {
    $this->certaintyAssessment = $certaintyAssessment;
  }
  /**
   * @return Feature
   */
  public function getCertaintyAssessment()
  {
    return $this->certaintyAssessment;
  }
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * linked_entities are candidate ontological concepts that this entity mention
   * may refer to. They are sorted by decreasing confidence.
   *
   * @param LinkedEntity[] $linkedEntities
   */
  public function setLinkedEntities($linkedEntities)
  {
    $this->linkedEntities = $linkedEntities;
  }
  /**
   * @return LinkedEntity[]
   */
  public function getLinkedEntities()
  {
    return $this->linkedEntities;
  }
  /**
   * mention_id uniquely identifies each entity mention in a single response.
   *
   * @param string $mentionId
   */
  public function setMentionId($mentionId)
  {
    $this->mentionId = $mentionId;
  }
  /**
   * @return string
   */
  public function getMentionId()
  {
    return $this->mentionId;
  }
  /**
   * The subject this entity mention relates to. Its value is one of: PATIENT,
   * FAMILY_MEMBER, OTHER
   *
   * @param Feature $subject
   */
  public function setSubject(Feature $subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return Feature
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * How this entity mention relates to the subject temporally. Its value is one
   * of: CURRENT, CLINICAL_HISTORY, FAMILY_HISTORY, UPCOMING, ALLERGY
   *
   * @param Feature $temporalAssessment
   */
  public function setTemporalAssessment(Feature $temporalAssessment)
  {
    $this->temporalAssessment = $temporalAssessment;
  }
  /**
   * @return Feature
   */
  public function getTemporalAssessment()
  {
    return $this->temporalAssessment;
  }
  /**
   * text is the location of the entity mention in the document.
   *
   * @param TextSpan $text
   */
  public function setText(TextSpan $text)
  {
    $this->text = $text;
  }
  /**
   * @return TextSpan
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The semantic type of the entity: UNKNOWN_ENTITY_TYPE, ALONE,
   * ANATOMICAL_STRUCTURE, ASSISTED_LIVING, BF_RESULT, BM_RESULT, BM_UNIT,
   * BM_VALUE, BODY_FUNCTION, BODY_MEASUREMENT, COMPLIANT, DOESNOT_FOLLOWUP,
   * FAMILY, FOLLOWSUP, LABORATORY_DATA, LAB_RESULT, LAB_UNIT, LAB_VALUE,
   * MEDICAL_DEVICE, MEDICINE, MED_DOSE, MED_DURATION, MED_FORM, MED_FREQUENCY,
   * MED_ROUTE, MED_STATUS, MED_STRENGTH, MED_TOTALDOSE, MED_UNIT,
   * NON_COMPLIANT, OTHER_LIVINGSTATUS, PROBLEM, PROCEDURE, PROCEDURE_RESULT,
   * PROC_METHOD, REASON_FOR_NONCOMPLIANCE, SEVERITY, SUBSTANCE_ABUSE,
   * UNCLEAR_FOLLOWUP.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityMention::class, 'Google_Service_CloudHealthcare_EntityMention');
