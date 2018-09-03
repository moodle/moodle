<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Php predictions processor
 *
 * @package   mlbackend_php
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mlbackend_php;

defined('MOODLE_INTERNAL') || die();

use Phpml\Preprocessing\Normalizer;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\ModelManager;

/**
 * PHP predictions processor.
 *
 * @package   mlbackend_php
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor implements \core_analytics\classifier, \core_analytics\regressor {

    /**
     * Size of training / prediction batches.
     */
    const BATCH_SIZE = 5000;

    /**
     * Number of train iterations.
     */
    const TRAIN_ITERATIONS = 500;

    /**
     * File name of the serialised model.
     */
    const MODEL_FILENAME = 'model.ser';

    /**
     * @var bool
     */
    protected $limitedsize = false;

    /**
     * Checks if the processor is ready to use.
     *
     * @return bool
     */
    public function is_ready() {
        if (version_compare(phpversion(), '7.0.0') < 0) {
            return get_string('errorphp7required', 'mlbackend_php');
        }
        return true;
    }

    /**
     * Delete the stored models.
     *
     * @param string $uniqueid
     * @param string $modelversionoutputdir
     * @return null
     */
    public function clear_model($uniqueid, $modelversionoutputdir) {
        remove_dir($modelversionoutputdir);
    }

    /**
     * Delete the output directory.
     *
     * @param string $modeloutputdir
     * @return null
     */
    public function delete_output_dir($modeloutputdir) {
        remove_dir($modeloutputdir);
    }

    /**
     * Train this processor classification model using the provided supervised learning dataset.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train_classification($uniqueid, \stored_file $dataset, $outputdir) {

        // Output directory is already unique to the model.
        $modelfilepath = $outputdir . DIRECTORY_SEPARATOR . self::MODEL_FILENAME;

        $modelmanager = new ModelManager();

        if (file_exists($modelfilepath)) {
            $classifier = $modelmanager->restoreFromFile($modelfilepath);
        } else {
            $classifier = new \Phpml\Classification\Linear\LogisticRegression(self::TRAIN_ITERATIONS, Normalizer::NORM_L2);
        }

        $fh = $dataset->get_content_file_handle();

        // The first lines are var names and the second one values.
        $metadata = $this->extract_metadata($fh);

        // Skip headers.
        fgets($fh);

        $samples = array();
        $targets = array();
        while (($data = fgetcsv($fh)) !== false) {
            $sampledata = array_map('floatval', $data);
            $samples[] = array_slice($sampledata, 0, $metadata['nfeatures']);
            $targets[] = intval($data[$metadata['nfeatures']]);

            $nsamples = count($samples);
            if ($nsamples === self::BATCH_SIZE) {
                // Training it batches to avoid running out of memory.

                $classifier->partialTrain($samples, $targets, array(0, 1));
                $samples = array();
                $targets = array();
            }
            if (empty($morethan1sample) && $nsamples > 1) {
                $morethan1sample = true;
            }
        }
        fclose($fh);

        if (empty($morethan1sample)) {
            $resultobj = new \stdClass();
            $resultobj->status = \core_analytics\model::NO_DATASET;
            $resultobj->info = array();
            return $resultobj;
        }

        // Train the remaining samples.
        if ($samples) {
            $classifier->partialTrain($samples, $targets, array(0, 1));
        }

        $resultobj = new \stdClass();
        $resultobj->status = \core_analytics\model::OK;
        $resultobj->info = array();

        // Store the trained model.
        $modelmanager->saveToFile($classifier, $modelfilepath);

        return $resultobj;
    }

    /**
     * Classifies the provided dataset samples.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function classify($uniqueid, \stored_file $dataset, $outputdir) {

        // Output directory is already unique to the model.
        $modelfilepath = $outputdir . DIRECTORY_SEPARATOR . self::MODEL_FILENAME;

        if (!file_exists($modelfilepath)) {
            throw new \moodle_exception('errorcantloadmodel', 'mlbackend_php', '', $modelfilepath);
        }

        $modelmanager = new ModelManager();
        $classifier = $modelmanager->restoreFromFile($modelfilepath);

        $fh = $dataset->get_content_file_handle();

        // The first lines are var names and the second one values.
        $metadata = $this->extract_metadata($fh);

        // Skip headers.
        fgets($fh);

        $sampleids = array();
        $samples = array();
        $predictions = array();
        while (($data = fgetcsv($fh)) !== false) {
            $sampledata = array_map('floatval', $data);
            $sampleids[] = $data[0];
            $samples[] = array_slice($sampledata, 1, $metadata['nfeatures']);

            if (count($samples) === self::BATCH_SIZE) {
                // Prediction it batches to avoid running out of memory.

                // Append predictions incrementally, we want $sampleids keys in sync with $predictions keys.
                $newpredictions = $classifier->predict($samples);
                foreach ($newpredictions as $prediction) {
                    array_push($predictions, $prediction);
                }
                $samples = array();
            }
        }
        fclose($fh);

        // Finish the remaining predictions.
        if ($samples) {
            $predictions = $predictions + $classifier->predict($samples);
        }

        $resultobj = new \stdClass();
        $resultobj->status = \core_analytics\model::OK;
        $resultobj->info = array();

        foreach ($predictions as $index => $prediction) {
            $resultobj->predictions[$index] = array($sampleids[$index], $prediction);
        }

        return $resultobj;
    }

    /**
     * Evaluates this processor classification model using the provided supervised learning dataset.
     *
     * During evaluation we need to shuffle the evaluation dataset samples to detect deviated results,
     * if the dataset is massive we can not load everything into memory. We know that 2GB is the
     * minimum memory limit we should have (\core_analytics\model::heavy_duty_mode), if we substract the memory
     * that we already consumed and the memory that Phpml algorithms will need we should still have at
     * least 500MB of memory, which should be enough to evaluate a model. In any case this is a robust
     * solution that will work for all sites but it should minimize memory limit problems. Site admins
     * can still set $CFG->mlbackend_php_no_evaluation_limits to true to skip this 500MB limit.
     *
     * @param string $uniqueid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function evaluate_classification($uniqueid, $maxdeviation, $niterations, \stored_file $dataset, $outputdir) {
        $fh = $dataset->get_content_file_handle();

        // The first lines are var names and the second one values.
        $metadata = $this->extract_metadata($fh);

        // Skip headers.
        fgets($fh);

        if (empty($CFG->mlbackend_php_no_evaluation_limits)) {
            $samplessize = 0;
            $limit = get_real_size('500MB');

            // Just an approximation, will depend on PHP version, compile options...
            // Double size + zval struct (6 bytes + 8 bytes + 16 bytes) + array bucket (96 bytes)
            // https://nikic.github.io/2011/12/12/How-big-are-PHP-arrays-really-Hint-BIG.html.
            $floatsize = (PHP_INT_SIZE * 2) + 6 + 8 + 16 + 96;
        }

        $samples = array();
        $targets = array();
        while (($data = fgetcsv($fh)) !== false) {
            $sampledata = array_map('floatval', $data);

            $samples[] = array_slice($sampledata, 0, $metadata['nfeatures']);
            $targets[] = intval($data[$metadata['nfeatures']]);

            if (empty($CFG->mlbackend_php_no_evaluation_limits)) {
                // We allow admins to disable evaluation memory usage limits by modifying config.php.

                // We will have plenty of missing values in the dataset so it should be a conservative approximation.
                $samplessize = $samplessize + (count($sampledata) * $floatsize);

                // Stop fetching more samples.
                if ($samplessize >= $limit) {
                    $this->limitedsize = true;
                    break;
                }
            }
        }
        fclose($fh);

        // We need at least 2 samples belonging to each target.
        $counts = array_count_values($targets);
        foreach ($counts as $count) {
            if ($count < 2) {
                $notenoughdata = true;
            }
        }
        if (!empty($notenoughdata)) {
            $resultobj = new \stdClass();
            $resultobj->status = \core_analytics\model::NOT_ENOUGH_DATA;
            $resultobj->score = 0;
            $resultobj->info = array(get_string('errornotenoughdata', 'mlbackend_php'));
            return $resultobj;
        }

        $phis = array();

        // Evaluate the model multiple times to confirm the results are not significantly random due to a short amount of data.
        for ($i = 0; $i < $niterations; $i++) {

            $classifier = new \Phpml\Classification\Linear\LogisticRegression(self::TRAIN_ITERATIONS, Normalizer::NORM_L2);

            // Split up the dataset in classifier and testing.
            $data = new RandomSplit(new ArrayDataset($samples, $targets), 0.2);

            $classifier->train($data->getTrainSamples(), $data->getTrainLabels());

            $predictedlabels = $classifier->predict($data->getTestSamples());
            $phis[] = $this->get_phi($data->getTestLabels(), $predictedlabels);
        }

        // Let's fill the results changing the returned status code depending on the phi-related calculated metrics.
        return $this->get_evaluation_result_object($dataset, $phis, $maxdeviation);
    }

    /**
     * Returns the results objects from all evaluations.
     *
     * @param \stored_file $dataset
     * @param array $phis
     * @param float $maxdeviation
     * @return \stdClass
     */
    protected function get_evaluation_result_object(\stored_file $dataset, $phis, $maxdeviation) {

        // Average phi of all evaluations as final score.
        if (count($phis) === 1) {
            $avgphi = reset($phis);
        } else {
            $avgphi = \Phpml\Math\Statistic\Mean::arithmetic($phis);
        }

        // Standard deviation should ideally be calculated against the area under the curve.
        if (count($phis) === 1) {
            $modeldev = 0;
        } else {
            $modeldev = \Phpml\Math\Statistic\StandardDeviation::population($phis);
        }

        // Let's fill the results object.
        $resultobj = new \stdClass();

        // Zero is ok, now we add other bits if something is not right.
        $resultobj->status = \core_analytics\model::OK;
        $resultobj->info = array();

        // Convert phi to a standard score (from -1 to 1 to a value between 0 and 1).
        $resultobj->score = ($avgphi + 1) / 2;

        // If each iteration results varied too much we need more data to confirm that this is a valid model.
        if ($modeldev > $maxdeviation) {
            $resultobj->status = $resultobj->status + \core_analytics\model::NOT_ENOUGH_DATA;
            $a = new \stdClass();
            $a->deviation = $modeldev;
            $a->accepteddeviation = $maxdeviation;
            $resultobj->info[] = get_string('errornotenoughdatadev', 'mlbackend_php', $a);
        }

        if ($resultobj->score < \core_analytics\model::MIN_SCORE) {
            $resultobj->status = $resultobj->status + \core_analytics\model::LOW_SCORE;
            $a = new \stdClass();
            $a->score = $resultobj->score;
            $a->minscore = \core_analytics\model::MIN_SCORE;
            $resultobj->info[] = get_string('errorlowscore', 'mlbackend_php', $a);
        }

        if ($this->limitedsize === true) {
            $resultobj->info[] = get_string('datasetsizelimited', 'mlbackend_php', display_size($dataset->get_filesize()));
        }

        return $resultobj;
    }

    /**
     * Train this processor regression model using the provided supervised learning dataset.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train_regression($uniqueid, \stored_file $dataset, $outputdir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Estimates linear values for the provided dataset samples.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param mixed $outputdir
     * @return void
     */
    public function estimate($uniqueid, \stored_file $dataset, $outputdir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Evaluates this processor regression model using the provided supervised learning dataset.
     *
     * @throws new \coding_exception
     * @param string $uniqueid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function evaluate_regression($uniqueid, $maxdeviation, $niterations, \stored_file $dataset, $outputdir) {
        throw new \coding_exception('This predictor does not support regression yet.');
    }

    /**
     * Returns the Phi correlation coefficient.
     *
     * @param array $testlabels
     * @param array $predictedlabels
     * @return float
     */
    protected function get_phi($testlabels, $predictedlabels) {

        // Binary here only as well.
        $matrix = \Phpml\Metric\ConfusionMatrix::compute($testlabels, $predictedlabels, array(0, 1));

        $tptn = $matrix[0][0] * $matrix[1][1];
        $fpfn = $matrix[1][0] * $matrix[0][1];
        $tpfp = $matrix[0][0] + $matrix[1][0];
        $tpfn = $matrix[0][0] + $matrix[0][1];
        $tnfp = $matrix[1][1] + $matrix[1][0];
        $tnfn = $matrix[1][1] + $matrix[0][1];
        if ($tpfp === 0 || $tpfn === 0 || $tnfp === 0 || $tnfn === 0) {
            $phi = 0;
        } else {
            $phi = ( $tptn - $fpfn ) / sqrt( $tpfp * $tpfn * $tnfp * $tnfn);
        }

        return $phi;
    }

    /**
     * Extracts metadata from the dataset file.
     *
     * The file poiter should be located at the top of the file.
     *
     * @param resource $fh
     * @return array
     */
    protected function extract_metadata($fh) {
        $metadata = fgetcsv($fh);
        return array_combine($metadata, fgetcsv($fh));
    }
}
