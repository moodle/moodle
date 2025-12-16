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

namespace Google\Service\CloudNaturalLanguage;

class XPSSpeechPreprocessStats extends \Google\Collection
{
  protected $collection_key = 'dataErrors';
  protected $dataErrorsType = XPSDataErrors::class;
  protected $dataErrorsDataType = 'array';
  /**
   * The number of rows marked HUMAN_LABELLED
   *
   * @var int
   */
  public $numHumanLabeledExamples;
  /**
   * The number of samples found in the previously recorded logs data.
   *
   * @var int
   */
  public $numLogsExamples;
  /**
   * The number of rows marked as MACHINE_TRANSCRIBED
   *
   * @var int
   */
  public $numMachineTranscribedExamples;
  /**
   * The number of examples labelled as TEST by Speech xps server.
   *
   * @var int
   */
  public $testExamplesCount;
  /**
   * The number of sentences in the test data set.
   *
   * @var int
   */
  public $testSentencesCount;
  /**
   * The number of words in the test data set.
   *
   * @var int
   */
  public $testWordsCount;
  /**
   * The number of examples labeled as TRAIN by Speech xps server.
   *
   * @var int
   */
  public $trainExamplesCount;
  /**
   * The number of sentences in the training data set.
   *
   * @var int
   */
  public $trainSentencesCount;
  /**
   * The number of words in the training data set.
   *
   * @var int
   */
  public $trainWordsCount;

  /**
   * Different types of data errors and the counts associated with them.
   *
   * @param XPSDataErrors[] $dataErrors
   */
  public function setDataErrors($dataErrors)
  {
    $this->dataErrors = $dataErrors;
  }
  /**
   * @return XPSDataErrors[]
   */
  public function getDataErrors()
  {
    return $this->dataErrors;
  }
  /**
   * The number of rows marked HUMAN_LABELLED
   *
   * @param int $numHumanLabeledExamples
   */
  public function setNumHumanLabeledExamples($numHumanLabeledExamples)
  {
    $this->numHumanLabeledExamples = $numHumanLabeledExamples;
  }
  /**
   * @return int
   */
  public function getNumHumanLabeledExamples()
  {
    return $this->numHumanLabeledExamples;
  }
  /**
   * The number of samples found in the previously recorded logs data.
   *
   * @param int $numLogsExamples
   */
  public function setNumLogsExamples($numLogsExamples)
  {
    $this->numLogsExamples = $numLogsExamples;
  }
  /**
   * @return int
   */
  public function getNumLogsExamples()
  {
    return $this->numLogsExamples;
  }
  /**
   * The number of rows marked as MACHINE_TRANSCRIBED
   *
   * @param int $numMachineTranscribedExamples
   */
  public function setNumMachineTranscribedExamples($numMachineTranscribedExamples)
  {
    $this->numMachineTranscribedExamples = $numMachineTranscribedExamples;
  }
  /**
   * @return int
   */
  public function getNumMachineTranscribedExamples()
  {
    return $this->numMachineTranscribedExamples;
  }
  /**
   * The number of examples labelled as TEST by Speech xps server.
   *
   * @param int $testExamplesCount
   */
  public function setTestExamplesCount($testExamplesCount)
  {
    $this->testExamplesCount = $testExamplesCount;
  }
  /**
   * @return int
   */
  public function getTestExamplesCount()
  {
    return $this->testExamplesCount;
  }
  /**
   * The number of sentences in the test data set.
   *
   * @param int $testSentencesCount
   */
  public function setTestSentencesCount($testSentencesCount)
  {
    $this->testSentencesCount = $testSentencesCount;
  }
  /**
   * @return int
   */
  public function getTestSentencesCount()
  {
    return $this->testSentencesCount;
  }
  /**
   * The number of words in the test data set.
   *
   * @param int $testWordsCount
   */
  public function setTestWordsCount($testWordsCount)
  {
    $this->testWordsCount = $testWordsCount;
  }
  /**
   * @return int
   */
  public function getTestWordsCount()
  {
    return $this->testWordsCount;
  }
  /**
   * The number of examples labeled as TRAIN by Speech xps server.
   *
   * @param int $trainExamplesCount
   */
  public function setTrainExamplesCount($trainExamplesCount)
  {
    $this->trainExamplesCount = $trainExamplesCount;
  }
  /**
   * @return int
   */
  public function getTrainExamplesCount()
  {
    return $this->trainExamplesCount;
  }
  /**
   * The number of sentences in the training data set.
   *
   * @param int $trainSentencesCount
   */
  public function setTrainSentencesCount($trainSentencesCount)
  {
    $this->trainSentencesCount = $trainSentencesCount;
  }
  /**
   * @return int
   */
  public function getTrainSentencesCount()
  {
    return $this->trainSentencesCount;
  }
  /**
   * The number of words in the training data set.
   *
   * @param int $trainWordsCount
   */
  public function setTrainWordsCount($trainWordsCount)
  {
    $this->trainWordsCount = $trainWordsCount;
  }
  /**
   * @return int
   */
  public function getTrainWordsCount()
  {
    return $this->trainWordsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSSpeechPreprocessStats::class, 'Google_Service_CloudNaturalLanguage_XPSSpeechPreprocessStats');
