<?php

namespace SimpleSAML\Module\statistics\Graph;

/**
 * \SimpleSAML\Module\statistics\Graph\GoogleCharts will help you to create a Google Chart
 * using the Google Charts API.
 *
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class GoogleCharts
{
    /** @var integer */
    private $x;

    /** @var integer */
    private $y;


    /**
     * Constructor.
     *
     * Takes dimension of graph as parameters. X and Y.
     *
     * @param integer $x    X dimension. Default 800.
     * @param integer $y    Y dimension. Default 350.
     */
    public function __construct($x = 800, $y = 350)
    {
        $this->x = $x;
        $this->y = $y;
    }


    /**
     * @param array $axis
     * @return string
     */
    private function encodeaxis(array $axis)
    {
        return join('|', $axis);
    }

    /**
     * t:10.0,58.0,95.0
     * @param array $datasets
     * @return string
     */
    private function encodedata(array $datasets)
    {
        $setstr = [];
        foreach ($datasets as $dataset) {
            $setstr[] = self::extEncode($dataset);
        }
        return 'e:' . join(',', $setstr);
    }


    /**
     * @param array $values
     * @return string
     */
    public static function extEncode(array $values) // $max = 4095, $min = 0
    {
        $extended_table = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
        $chardata = '';
        $delta = 4095;
        $size = strlen($extended_table);

        foreach ($values as $k => $v) {
            if ($v >= 0 && $v <= 100) {
                $first = substr($extended_table, intval(($delta * $v / 100) / $size), 1);
                $second = substr($extended_table, intval(($delta * $v / 100) % $size), 1);
                $chardata .= "$first$second";
            } else {
                $chardata .= '__'; // Value out of max range;
            }
        }
        return $chardata;
    }


    /**
     * Generate a Google Charts URL which points to a generated image.
     * More documentation on Google Charts here:
     *   http://code.google.com/apis/chart/
     *
     * @param array $axis        Axis
     * @param array $axispos     Axis positions
     * @param array $datasets    Datasets values
     * @param array $maxes       Max value. Will be the topmost value on the Y-axis.
     * @return string
     */
    public function show(array $axis, array $axispos, array $datasets, array $maxes)
    {
        $labeld = '&chxt=x,y' . '&chxr=0,0,1|1,0,' . $maxes[0];
        if (count($datasets) > 1) {
            if (count($datasets) !== count($maxes)) {
                throw new \Exception('Incorrect number of max calculations for graph plotting.');
            }
            $labeld = '&chxt=x,y,r' . '&chxr=0,0,1|1,0,' . $maxes[0] . '|2,0,' . $maxes[1];
        }

        $url = 'https://chart.apis.google.com/chart?' .
            // Dimension of graph. Default is 800x350
            'chs=' . $this->x . 'x' . $this->y .

            // Dateset values
            '&chd=' . $this->encodedata($datasets) .

            // Fill area...
            '&chco=ff5c00,cca600' .
            '&chls=1,1,0|1,6,3' .

            // chart type is linechart
            '&cht=lc' .
            $labeld .
            '&chxl=0:|' . $this->encodeaxis($axis) . #.$'|1:||top' .
            '&chxp=0,' . join(',', $axispos) .
            '&chg=' . (2400 / (count($datasets[0]) - 1)) . ',-1,3,3'; // lines

        return $url;
    }


    /**
     * @param array $axis
     * @param array $datasets
     * @return string
     */
    public function showPie(array $axis, array $datasets)
    {
        $url = 'https://chart.apis.google.com/chart?' .

        // Dimension of graph. Default is 800x350
        'chs=' . $this->x . 'x' . $this->y .

        // Dateset values.
        '&chd=' . $this->encodedata([$datasets]) .

        // chart type is linechart
        '&cht=p' .

        '&chl=' . $this->encodeaxis($axis);

        return $url;
    }


    /**
     * Takes a input value, and generates a value that suits better as a max
     * value on the Y-axis. In example 37.6 will not make a good max value, instead
     * it will return 40. It will always return an equal or larger number than it gets
     * as input.
     *
     * Here is some test code:
     * <code>
     *      $foo = array(
     *          0, 2, 2.3, 2.6, 6, 10, 15, 98, 198, 256, 487, 563, 763, 801, 899, 999, 987, 198234.485, 283746
     *      );
     *      foreach ($foo as $f) {
     *          echo '<p>'.$f.' => '.\SimpleSAML\Module\statistics\Graph\GoogleCharts::roof($f);
     *      }
     * </code>
     *
     * @param int $max    Input value.
     * @return int
     */
    public static function roof($max)
    {
        $mul = 1;

        if ($max < 1) {
            return 1;
        }

        $t = intval(ceil($max));
        while ($t > 100) {
            $t /= 10;
            $mul *= 10;
        }

        $maxGridLines = 10;
        $candidates = [1, 2, 5, 10, 20, 25, 50, 100];

        foreach ($candidates as $c) {
            if ($t / $c < $maxGridLines) {
                $tick_y = $c * $mul;
                $target_top = intval(ceil($max / $tick_y) * $tick_y);
                return $target_top;
            }
        }
        return 1;
    }
}
