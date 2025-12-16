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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Finding extends \Google\Model
{
  /**
   * Default value; same as POSSIBLE.
   */
  public const LIKELIHOOD_LIKELIHOOD_UNSPECIFIED = 'LIKELIHOOD_UNSPECIFIED';
  /**
   * Highest chance of a false positive.
   */
  public const LIKELIHOOD_VERY_UNLIKELY = 'VERY_UNLIKELY';
  /**
   * High chance of a false positive.
   */
  public const LIKELIHOOD_UNLIKELY = 'UNLIKELY';
  /**
   * Some matching signals. The default value.
   */
  public const LIKELIHOOD_POSSIBLE = 'POSSIBLE';
  /**
   * Low chance of a false positive.
   */
  public const LIKELIHOOD_LIKELY = 'LIKELY';
  /**
   * Confidence level is high. Lowest chance of a false positive.
   */
  public const LIKELIHOOD_VERY_LIKELY = 'VERY_LIKELY';
  /**
   * Timestamp when finding was detected.
   *
   * @var string
   */
  public $createTime;
  /**
   * The unique finding id.
   *
   * @var string
   */
  public $findingId;
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';
  /**
   * Time the job started that produced this finding.
   *
   * @var string
   */
  public $jobCreateTime;
  /**
   * The job that stored the finding.
   *
   * @var string
   */
  public $jobName;
  /**
   * The labels associated with this `Finding`. Label keys must be between 1 and
   * 63 characters long and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values must be between 0 and 63
   * characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be associated
   * with a given finding. Examples: * `"environment" : "production"` *
   * `"pipeline" : "etl"`
   *
   * @var string[]
   */
  public $labels;
  /**
   * Confidence of how likely it is that the `info_type` is correct.
   *
   * @var string
   */
  public $likelihood;
  protected $locationType = GooglePrivacyDlpV2Location::class;
  protected $locationDataType = '';
  /**
   * Resource name in format
   * projects/{project}/locations/{location}/findings/{finding} Populated only
   * when viewing persisted findings.
   *
   * @var string
   */
  public $name;
  /**
   * The content that was found. Even if the content is not textual, it may be
   * converted to a textual representation here. Provided if `include_quote` is
   * true and the finding is less than or equal to 4096 bytes long. If the
   * finding exceeds 4096 bytes in length, the quote may be omitted.
   *
   * @var string
   */
  public $quote;
  protected $quoteInfoType = GooglePrivacyDlpV2QuoteInfo::class;
  protected $quoteInfoDataType = '';
  /**
   * The job that stored the finding.
   *
   * @var string
   */
  public $resourceName;
  /**
   * Job trigger name, if applicable, for this finding.
   *
   * @var string
   */
  public $triggerName;

  /**
   * Timestamp when finding was detected.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The unique finding id.
   *
   * @param string $findingId
   */
  public function setFindingId($findingId)
  {
    $this->findingId = $findingId;
  }
  /**
   * @return string
   */
  public function getFindingId()
  {
    return $this->findingId;
  }
  /**
   * The type of content that might have been found. Provided if
   * `excluded_types` is false.
   *
   * @param GooglePrivacyDlpV2InfoType $infoType
   */
  public function setInfoType(GooglePrivacyDlpV2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * Time the job started that produced this finding.
   *
   * @param string $jobCreateTime
   */
  public function setJobCreateTime($jobCreateTime)
  {
    $this->jobCreateTime = $jobCreateTime;
  }
  /**
   * @return string
   */
  public function getJobCreateTime()
  {
    return $this->jobCreateTime;
  }
  /**
   * The job that stored the finding.
   *
   * @param string $jobName
   */
  public function setJobName($jobName)
  {
    $this->jobName = $jobName;
  }
  /**
   * @return string
   */
  public function getJobName()
  {
    return $this->jobName;
  }
  /**
   * The labels associated with this `Finding`. Label keys must be between 1 and
   * 63 characters long and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values must be between 0 and 63
   * characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be associated
   * with a given finding. Examples: * `"environment" : "production"` *
   * `"pipeline" : "etl"`
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Confidence of how likely it is that the `info_type` is correct.
   *
   * Accepted values: LIKELIHOOD_UNSPECIFIED, VERY_UNLIKELY, UNLIKELY, POSSIBLE,
   * LIKELY, VERY_LIKELY
   *
   * @param self::LIKELIHOOD_* $likelihood
   */
  public function setLikelihood($likelihood)
  {
    $this->likelihood = $likelihood;
  }
  /**
   * @return self::LIKELIHOOD_*
   */
  public function getLikelihood()
  {
    return $this->likelihood;
  }
  /**
   * Where the content was found.
   *
   * @param GooglePrivacyDlpV2Location $location
   */
  public function setLocation(GooglePrivacyDlpV2Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return GooglePrivacyDlpV2Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Resource name in format
   * projects/{project}/locations/{location}/findings/{finding} Populated only
   * when viewing persisted findings.
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
   * The content that was found. Even if the content is not textual, it may be
   * converted to a textual representation here. Provided if `include_quote` is
   * true and the finding is less than or equal to 4096 bytes long. If the
   * finding exceeds 4096 bytes in length, the quote may be omitted.
   *
   * @param string $quote
   */
  public function setQuote($quote)
  {
    $this->quote = $quote;
  }
  /**
   * @return string
   */
  public function getQuote()
  {
    return $this->quote;
  }
  /**
   * Contains data parsed from quotes. Only populated if include_quote was set
   * to true and a supported infoType was requested. Currently supported
   * infoTypes: DATE, DATE_OF_BIRTH and TIME.
   *
   * @param GooglePrivacyDlpV2QuoteInfo $quoteInfo
   */
  public function setQuoteInfo(GooglePrivacyDlpV2QuoteInfo $quoteInfo)
  {
    $this->quoteInfo = $quoteInfo;
  }
  /**
   * @return GooglePrivacyDlpV2QuoteInfo
   */
  public function getQuoteInfo()
  {
    return $this->quoteInfo;
  }
  /**
   * The job that stored the finding.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Job trigger name, if applicable, for this finding.
   *
   * @param string $triggerName
   */
  public function setTriggerName($triggerName)
  {
    $this->triggerName = $triggerName;
  }
  /**
   * @return string
   */
  public function getTriggerName()
  {
    return $this->triggerName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Finding::class, 'Google_Service_DLP_GooglePrivacyDlpV2Finding');
