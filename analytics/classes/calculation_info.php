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
 * Extra information generated during the analysis by calculable elements.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Extra information generated during the analysis by calculable elements.
 *
 * The main purpose of this request cache is to allow calculable elements to
 * store data during their calculations for further use at a later stage efficiently.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculation_info {

    /**
     * @var array
     */
    private $info = [];

    /**
     * @var mixed[]
     */
    private $samplesinfo = [];

    /**
     * Adds info related to the current calculation for later use when generating insights.
     *
     * Note that the data in $info array is reused across multiple samples, if you want to add data just for this
     * sample you can use the sample id as key.
     *
     * We store two different arrays so objects that appear multiple times for different samples
     * appear just once in memory.
     *
     * @param int    $sampleid  The sample id this data is associated with
     * @param array  $info      The data. Indexed by an id unique across the site. E.g. an activity id.
     * @return null
     */
    public function add_shared(int $sampleid, array $info) {

        // We can safely overwrite the existing keys because the provided info is supposed to be unique
        // for the indicator.
        $this->info = $info + $this->info;

        // We also need to store the association between the info provided and the sample.
        $this->samplesinfo[$sampleid] = array_keys($info);
    }

    /**
     * Stores in MUC the previously added data and it associates it to the provided $calculable.
     *
     * @param  \core_analytics\calculable                $calculable
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  int                                       $rangeindex
     * @return null
     */
    public function save(\core_analytics\calculable $calculable, \core_analytics\local\time_splitting\base $timesplitting,
            int $rangeindex) {

        $calculableclass = get_class($calculable);
        $cache = \cache::make('core', 'calculablesinfo');

        foreach ($this->info as $key => $value) {
            $datakey = self::get_data_key($calculableclass, $key);

            // We do not overwrite existing data.
            if (!$cache->has($datakey)) {
                $cache->set($datakey, $value);
            }
        }

        foreach ($this->samplesinfo as $sampleid => $infokeys) {
            $uniquesampleid = $timesplitting->append_rangeindex($sampleid, $rangeindex);
            $samplekey = self::get_sample_key($uniquesampleid);

            // Update the cached data adding the new indicator data.
            $cacheddata = $cache->get($samplekey) ?: [];
            $cacheddata[$calculableclass] = $infokeys;
            $cache->set($samplekey, $cacheddata);
        }

        // Empty the in-memory arrays now that it is in the cache.
        $this->info = [];
        $this->samplesinfo = [];
    }

    /**
     * Pulls the info related to the provided records out from the cache.
     *
     * Note that this function purges 'calculablesinfo' cache.
     *
     * @param  \stdClass[] $predictionrecords
     * @return array|false
     */
    public static function pull_info(array $predictionrecords) {

        $cache = \cache::make('core', 'calculablesinfo');

        foreach ($predictionrecords as $uniquesampleid => $predictionrecord) {

            $sampleid = $predictionrecord->sampleid;

            $sampleinfo = $cache->get(self::get_sample_key($uniquesampleid));

            // MUC returns (or should return) copies of the data and we want a single copy of it so
            // we store the data here and reference it from each sample. Samples data should not be
            // changed afterwards.
            $data = [];

            if ($sampleinfo) {
                foreach ($sampleinfo as $calculableclass => $infokeys) {

                    foreach ($infokeys as $infokey) {

                        // We don't need to retrieve data back from MUC if we already have it.
                        if (!isset($data[$calculableclass][$infokey])) {
                            $datakey = self::get_data_key($calculableclass, $infokey);
                            $data[$calculableclass][$infokey] = $cache->get($datakey);
                        }

                        $samplesdatakey = $calculableclass . ':extradata';
                        $samplesdata[$sampleid][$samplesdatakey][$infokey] = & $data[$calculableclass][$infokey];
                    }
                }
            }
        }

        // Free memory ASAP. We can replace the purge call by a delete_many if we are interested on allowing
        // multiple calls to pull_info passing in different $sampleids.
        $cache->purge();

        if (empty($samplesdata)) {
            return false;
        }

        return $samplesdata;
    }

    /**
     * Gets the key used to store data.
     *
     * @param  string       $calculableclass
     * @param  string|int   $key
     * @return string
     */
    private static function get_data_key(string $calculableclass, $key): string {
        return 'data:' . $calculableclass . ':' . $key;
    }

    /**
     * Gets the key used to store samples.
     *
     * @param  string $uniquesampleid
     * @return string
     */
    private static function get_sample_key(string $uniquesampleid): string {
        return 'sample:' . $uniquesampleid;
    }
}