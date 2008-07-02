<?php
/**
 * curl class
 *
 * This is a wrapper class for curl, it is easy to use:
 * $c = new curl();
 * // HTTP GET Method
 * $html = $c->get('http://moodle.org');
 * // HTTP POST Method
 * $html = $c->post('http://moodle.org/', array('q'=>'words', 'name'=>'moodle'));
 *
 * @author Dongsheng Cai <dongsheng@cvs.moodle.org>
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class curl {
    private $header;
    private $options;
    private $error;
    private $debug = false;

    public function __construct($options = array()){

        if(!function_exists('curl_init')) {
            $this->error = 'cURL module must be enabled!';
            trigger_error($this->error, E_USER_ERROR);
            return false;
        }

        $this->header  = array();
        $this->options = array();
        $this->setopt();
    }

    /**
     * Set curl options
     * 
     * @param array $options If array is null, this function will
     * reset the options to default value.
     *
     */
    public function setopt($options = array()) {
        $this->options['CURLOPT_HEADER']            = 0;
        $this->options['CURLOPT_NOBODY']            = 0;
        $this->options['CURLOPT_USERAGENT']         = 'MoodleBot/1.0';
        $this->options['CURLOPT_FOLLOWLOCATION']    = 1;
        $this->options['CURLOPT_MAXREDIRS']         = 10;
        $this->options['CURLOPT_TIMEOUT']           = 120;
        $this->options['CURLOPT_ENCODING']          = '';
        $this->options['CURLOPT_RETURNTRANSFER']    = 1;
        $this->options['CURLOPT_BINARYTRANSFER']    = 0;
        $this->options['CURLOPT_SSL_VERIFYPEER']    = 0;

        if (is_array($options)) {
            foreach($options as $name => $val) 
                $this->options[$name] = $val;
        }

    }

    private function resetopt() 
    {
        unset($this->options['CURLOPT_HTTPGET']);
        unset($this->options['CURLOPT_POST']);
        unset($this->options['CURLOPT_POSTFIELDS']);
        unset($this->options['CURLOPT_PUT']);
        unset($this->options['CURLOPT_INFILE']);
        unset($this->options['CURLOPT_INFILESIZE']);
        unset($this->options['CURLOPT_CUSTOMREQUEST']);
    }
    /**
     * Set HTTP Header
     * 
     * @param array $headers 
     *
     */
    public function setheader($header) {
        if (is_array($header)){
            foreach ($header as $v) {
                $this->setheader($v);
            }
        } else {
            $this->header[] = $val;
        }
    }

    protected function request($url, $options = array()){

        // create curl instance
        $curl = curl_init($url);
        // reset before set options
        $this->setopt($options);

        // set options
        foreach($this->options as $name => $val) {
            if (is_string($name)) {
                $name = constant(strtoupper($name));
            }
            curl_setopt($curl, $name, $val);
        }

        if($this->debug){
            var_dump($this->options);
        }

        // set headers
        if(!empty($this->header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        }

        $ret  = curl_exec($curl);

        if($this->debug){
            var_dump($ret);
        }

        $info = curl_getinfo($curl);

        curl_close($curl);

        if(!empty($ret)){
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * HTTP HEAD method
     */
    public function head($url, $options = array()){
        $this->setopt(array('CURLOPT_HTTPGET'=>1));
        $this->setopt(array('CURLOPT_HEADER' =>1));
        $this->setopt(array('CURLOPT_NOBODY' =>1));
        return $this->request($url, $options);
    }

    /**
     * HTTP POST method
     */
    public function post($url, $params = array(), $options = array()){
        $this->setopt(array('CURLOPT_POST'=>1));
        $this->setopt(array('CURLOPT_POSTFIELDS'=>$params));
        return $this->request($url, $options);
    }

    /**
     * HTTP GET method
     */
    public function get($url, $params = array(), $options = array()){
        if(!empty($params)){
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= http_build_query($params);
        }
        $this->setopt(array('CURLOPT_HTTPGET'=>1));
        return $this->request($url, $options);
    }

    /**
     * HTTP PUT method
     */
    public function put($url, $params = array(), $options = array()){
        $file = $params['file'];
        if(!is_file($file)){
            return null;
        }
        $fp = fopen($file, 'rw');
        $this->setopt(array('CURLOPT_PUT'=>1));
        $this->setopt(array('CURLOPT_INFILE'=>$fp));
        $this->setopt(array('CURLOPT_INFILESIZE'=>-1));
        if (!isset($this->options['CURLOPT_USERPWD'])){
            $this->setopt(array('CURLOPT_USERPWD'=>'anonymous: noreply@moodle.org'));
        }
        $ret = $this->request($url, $options);
        return $ret;
    }

    /**
     * HTTP DELETE method
     */
    public function delete($url, $options = array()){
        $this->setopt(array('CURLOPT_CUSTOMREQUEST'=>'DELETE'));
        if (!isset($this->options['CURLOPT_USERPWD'])) {
            $this->options['CURLOPT_USERPWD'] = 'anonymous: noreply@moodle.org';
        }
        $ret = $this->request($url, $options);
        return $ret;
    }
}
/*
$c = new curl();

echo '<div style="clear:both">---------------------This is a line---------------------</a>';

$z = $c->get('http://foleo.appspot.com/');

echo '<div><textarea rows="29" cols="99">';
echo htmlentities($z);
echo '</textarea></div>';
*/
