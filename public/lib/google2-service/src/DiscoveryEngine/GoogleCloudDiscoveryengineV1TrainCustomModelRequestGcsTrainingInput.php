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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput extends \Google\Model
{
  /**
   * The Cloud Storage corpus data which could be associated in train data. The
   * data path format is `gs:`. A newline delimited jsonl/ndjson file. For
   * search-tuning model, each line should have the _id, title and text.
   * Example: `{"_id": "doc1", title: "relevant doc", "text": "relevant text"}`
   *
   * @var string
   */
  public $corpusDataPath;
  /**
   * The gcs query data which could be associated in train data. The data path
   * format is `gs:`. A newline delimited jsonl/ndjson file. For search-tuning
   * model, each line should have the _id and text. Example: {"_id": "query1",
   * "text": "example query"}
   *
   * @var string
   */
  public $queryDataPath;
  /**
   * Cloud Storage test data. Same format as train_data_path. If not provided, a
   * random 80/20 train/test split will be performed on train_data_path.
   *
   * @var string
   */
  public $testDataPath;
  /**
   * Cloud Storage training data path whose format should be `gs:`. The file
   * should be in tsv format. Each line should have the doc_id and query_id and
   * score (number). For search-tuning model, it should have the query-id
   * corpus-id score as tsv file header. The score should be a number in `[0,
   * inf+)`. The larger the number is, the more relevant the pair is. Example: *
   * `query-id\tcorpus-id\tscore` * `query1\tdoc1\t1`
   *
   * @var string
   */
  public $trainDataPath;

  /**
   * The Cloud Storage corpus data which could be associated in train data. The
   * data path format is `gs:`. A newline delimited jsonl/ndjson file. For
   * search-tuning model, each line should have the _id, title and text.
   * Example: `{"_id": "doc1", title: "relevant doc", "text": "relevant text"}`
   *
   * @param string $corpusDataPath
   */
  public function setCorpusDataPath($corpusDataPath)
  {
    $this->corpusDataPath = $corpusDataPath;
  }
  /**
   * @return string
   */
  public function getCorpusDataPath()
  {
    return $this->corpusDataPath;
  }
  /**
   * The gcs query data which could be associated in train data. The data path
   * format is `gs:`. A newline delimited jsonl/ndjson file. For search-tuning
   * model, each line should have the _id and text. Example: {"_id": "query1",
   * "text": "example query"}
   *
   * @param string $queryDataPath
   */
  public function setQueryDataPath($queryDataPath)
  {
    $this->queryDataPath = $queryDataPath;
  }
  /**
   * @return string
   */
  public function getQueryDataPath()
  {
    return $this->queryDataPath;
  }
  /**
   * Cloud Storage test data. Same format as train_data_path. If not provided, a
   * random 80/20 train/test split will be performed on train_data_path.
   *
   * @param string $testDataPath
   */
  public function setTestDataPath($testDataPath)
  {
    $this->testDataPath = $testDataPath;
  }
  /**
   * @return string
   */
  public function getTestDataPath()
  {
    return $this->testDataPath;
  }
  /**
   * Cloud Storage training data path whose format should be `gs:`. The file
   * should be in tsv format. Each line should have the doc_id and query_id and
   * score (number). For search-tuning model, it should have the query-id
   * corpus-id score as tsv file header. The score should be a number in `[0,
   * inf+)`. The larger the number is, the more relevant the pair is. Example: *
   * `query-id\tcorpus-id\tscore` * `query1\tdoc1\t1`
   *
   * @param string $trainDataPath
   */
  public function setTrainDataPath($trainDataPath)
  {
    $this->trainDataPath = $trainDataPath;
  }
  /**
   * @return string
   */
  public function getTrainDataPath()
  {
    return $this->trainDataPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput');
