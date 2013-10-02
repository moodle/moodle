<?php
    
defined('MOODLE_INTERNAL') || die();

/**
 * Workshop module renderer class
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workshopeval_calibrated_renderer extends plugin_renderer_base {
    
    protected function render_workshop_calibrated_evaluation_explanation(workshop_calibrated_evaluation_explanation $explanation) {
        
        
        $o = $this->output->container_start('inline-block');
        
        $o .= "<table class='explanation'>";
        $o .= "<tr><th>Criterion</th><th>Given score</th><th>Reference score</th><th>Difference</th></tr>";
        
        foreach($explanation->reference_values as $k => $v) {
            $sub = $explanation->references[$k];
            $o .= "<tr><th colspan='4' class='subheading'>{$sub->title}</th></tr>";

            foreach($v as $i => $a) {
                $title = trim(strip_tags($a[0]->title));
                $info = $explanation->diminfo[$i];
                if(strlen($title) > 300) $title = substr($title, 0, 300) . "...";
                
                $g = $explanation->gradedecimals;
                $wrongness = $explanation->normalize_grade($info, $a[3]);
                
                $theirscore = sprintf("%1.{$g}f <small>/ %1.{$g}f</small>", $a[1], $info->max);
                $myscore = sprintf("%1.{$g}f <small>/ %1.{$g}f</small>", $a[2], $info->max);
                $difference = sprintf("%1.{$g}f <small>(%1.2f%%)</small>", $a[3], $wrongness);
                
                $wrongness_color = $this->get_colour(100 - $wrongness, 100, 1);
                $o .= "<tr><th scope='row'>$title</td><td>$theirscore</td><td>$myscore</td><td style='background-color:#$wrongness_color'>$difference</td></tr>";
            }
            
        }
        
        $wrongness_color = $this->get_colour(100 - $explanation->raw_average, 100, 1);
        $raw_average = sprintf("%1.2f",$explanation->raw_average);
        $o .= "<tr><th scope='row' colspan='3'>Average Difference</th><td style='background-color:#$wrongness_color'>$raw_average%</td></tr>";
        
        $o .= "</table>";
        
        $o .= $this->output->container_end();
        
        $o .= "\n";
        
        $o .= $this->output->container_start('inline-block');
        
        $o .= "<table class='explanation equations'>";
        
        $accuracy = sprintf("%1.2f%%", 100 - $explanation->raw_average);
        $o .= "<tr class='big'><th>Raw Accuracy (x)</th><td>$accuracy</td></tr>";
        
        $o .= "<tr><th>Your accuracy factor</th><td>$explanation->accuracy</td>";
        
        $accuracy_curve = workshop_calibrated_evaluation::$grading_curves[$explanation->accuracy];
        $o .= "<tr><th>Equivalent scaling curve (n)</th><td>$accuracy_curve</td>";
        
        $calculation = $explanation->accuracy > 5 ?  "1 - ( 1 - x )<sup>n</sup>" : "x<sup>1/n</sup>";
        if ($explanation->accuracy == 5) $calculation = "No scaling";
        $o .= "<tr><th>Scaling equation</th><td>$calculation</td></tr>";
        
        $scaled_accuracy = sprintf("%1.2f%%", $explanation->scaled_average);
        $o .= "<tr class='big'><th>Scaled Accuracy</th><td>$scaled_accuracy</td></tr>";
        
        $mean_deviation = sprintf("%1.2f%%", $explanation->mad);
        $o .= "<tr><th>Mean deviation (1 - y)</th><td>$mean_deviation</td></tr>";
        
        $o .= "<tr><th>Your consistency factor</th><td>$explanation->consistency</td>";
        
        $consistency_curve = workshop_calibrated_evaluation::$grading_curves[9 - $explanation->consistency];
        $o .= "<tr><th>Equivalent scaling curve (n)</th><td>$consistency_curve</td>";
        
        $calculation = "ny - n + 1";
        if ($explanation->consistency == 9) $calculation = "No scaling";
        $o .= "<tr><th>Scaling equation</th><td>$calculation</td></tr>";
        
        $consistency_multiplier = sprintf("%1.3f", $explanation->consistency_multiplier);
        $o .= "<tr class='big'><th>Consistency Multiplier</th><td>$consistency_multiplier</td></tr>";
        
        $final_score = sprintf("%1.2f%%", $explanation->final_score);
        if ($explanation->finalscoreoutof) {
            $final_score_points = $explanation->final_score / 100 * $explanation->finalscoreoutof;
            $final_score .= sprintf("<br/>%1.0f / %1.0f", $final_score_points, $explanation->finalscoreoutof);
        }
        $o .= "<tr class='big bold'><th>Final Score</th><td>$final_score</td></tr>";
        
        $o .= "</table>";
        
        $o .= $this->output->container_end();
        
        return $o;
        
    }
    
    protected function get_colour($i,$n,$darklight=0) {
        //base hue: 0, max hue: 120
        $hue = $i / ($n - 1) * 120;
        $hue = pow($hue,1.5)/sqrt(120); // this biases the curve a little bit toward the red/yellow end
        if($darklight == -1) {
            $s = 1.0; $v = 0.8;
        } elseif ($darklight == 0) {
            $s = 0.9; $v = 0.9;
        } elseif ($darklight == 1) {
            $s = 0.5; $v = 1.0;
        }
        return $this->hsv_to_rgb($hue,$s,$v);
    }
    
    /**
     * @param float $h from 0 to 360
     * @param float $s from 0 to 1
     * @param float $v from 0 to 1
     */
    private function hsv_to_rgb($h,$s,$v) {
        //folowing the formulae found at http://en.wikipedia.org/wiki/HSV_color_space#Converting_to_RGB
        $c = $v * $s; //chroma
        $hp = $h / 60;
        

        if($hp < 0) {
            return array(0,0,0); //fucked the input, return black
        } elseif ($hp < 1) {
            $x = $c * $hp;
            $rgb = array( $c, $x, 0);
        } elseif ($hp < 2) {
            $x = $c * (1 - ($hp - 1));
            $rgb = array( $x, $c, 0);
        } elseif ($hp < 3) {
            $x = $c * ($hp - 2);
            $rgb = array( 0, $c, $x);
        } elseif ($hp < 4) {
            $x = $c * (1 - ($hp - 3));
            $rgb = array( 0, $x, $c);
        } elseif ($hp < 5) {
            $x = $c * ($hp - 4);
            $rgb = array( $x, 0, $c);
        } elseif ($hp <= 6) {
            $x = $c * (1 - ($hp - 5));
            $rgb = array( $c, 0, $x);
        }
        
        $m = $v - $c;
        foreach($rgb as $k => $v) {
            $rgb[$k] = $v + $m;
        }
        
        return $this->rgb_to_hex($rgb);
        
    }
    
    private function rgb_to_hex($rgb) {
        list($r,$g,$b) = $rgb;
        return sprintf('%02X%02X%02X',$r * 255,$g * 255,$b * 255);
    }
}
    
?>