<?php
/**
 * RESTful cURL class
 *
 * This is a wrapper class for curl, it is quite easy to use:
 *
 * $c = new curl();
 * // HTTP GET Method
 * $html = $c->get('http://example.com');
 * // HTTP POST Method
 * $html = $c->post('http://example.com/', array('q'=>'words', 'name'=>'moodle'));
 * // HTTP PUT Method
 * $html = $c->put('http://example.com/', array('file'=>'/var/www/test.txt');
 *
 * @author Dongsheng Cai <dongsheng@cvs.moodle.org>
 * @version 0.2 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class curl {
    private $options;
    private $debug    = false;
    private $cookie   = false;
    public  $version  = '0.2 dev';
    public  $response = array();
    public  $header   = array();
    public  $info;
    public  $error;
    public function __construct($options = array()){
        if(!function_exists('curl_init')) {
            $this->error = 'cURL module must be enabled!';
            trigger_error($this->error, E_USER_ERROR);
            return false;
        }
        if(!empty($options['debug'])) {
            $this->debug = true;
        }
        $this->debug = false;
        if(!empty($options['cookie'])) {
            if(is_file($options['cookie'])) {
                $this->cookie = $options['cookie'];
            }
        }
        $this->resetopt();
    }
    public function resetopt(){
        $this->options = array();
        $this->options['CURLOPT_USERAGENT']         = 'MoodleBot/1.0';
        // True to include the header in the output 
        $this->options['CURLOPT_HEADER']            = 0;
        // True to Exclude the body from the output 
        $this->options['CURLOPT_NOBODY']            = 0;
        // TRUE to follow any "Location: " header that the server 
        // sends as part of the HTTP header (note this is recursive, 
        // PHP will follow as many "Location: " headers that it is sent, 
        // unless CURLOPT_MAXREDIRS is set).  
        $this->options['CURLOPT_FOLLOWLOCATION']    = 1;
        $this->options['CURLOPT_MAXREDIRS']         = 10;
        $this->options['CURLOPT_ENCODING']          = '';
        // TRUE to return the transfer as a string of the return 
        // value of curl_exec() instead of outputting it out directly.  
        $this->options['CURLOPT_RETURNTRANSFER']    = 1;
        $this->options['CURLOPT_BINARYTRANSFER']    = 0;
        $this->options['CURLOPT_SSL_VERIFYPEER']    = 0;
        $this->options['CURLOPT_SSL_VERIFYHOST']    = 2;
        $this->options['CURLOPT_TIMEOUT']           = 120;
    }

    /**
     * Reset Cookie
     * 
     * @param array $options If array is null, this function will
     * reset the options to default value.
     *
     */
    public function resetcookie() {
        if(!empty($this->cookie)) {
            if(is_file($this->cookie)) {
                $fp = fopen($this->cookie, 'w');
                if(!empty($fp)) {
                    fwrite($fp, '');
                    fclose($fp);
                }
            }
        }
    }

    /**
     * Set curl options
     * 
     * @param array $options If array is null, this function will
     * reset the options to default value.
     *
     */
    public function setopt($options = array()) {
        if (is_array($options)) {
            foreach($options as $name => $val){
                if(stripos($name, 'CURLOPT_') === false) {
                    $name = strtoupper('CURLOPT_'.$name);
                }
                $this->options[$name] = $val;
            } 
        }
    }
    /**
     * Reset http method
     *
     */
    public function cleanopt(){
        unset($this->options['CURLOPT_HTTPGET']);
        unset($this->options['CURLOPT_POST']);
        unset($this->options['CURLOPT_POSTFIELDS']);
        unset($this->options['CURLOPT_PUT']);
        unset($this->options['CURLOPT_INFILE']);
        unset($this->options['CURLOPT_INFILESIZE']);
        unset($this->options['CURLOPT_CUSTOMREQUEST']);
    }

    /**
     * Set HTTP Request Header
     * 
     * @param array $headers 
     *
     */
    public function setHeader($header) {
        if (is_array($header)){
            foreach ($header as $v) {
                $this->setHeader($v);
            }
        } else {
            $this->header[] = $header;
        }
    }
    /**
     * Set HTTP Response Header
     * 
     */
    public function getResponse(){
        return $this->response;
    }
    /**
     * private callback function
     * Formatting HTTP Response Header
     * 
     */
    private function formatHeader($ch, $header) 
    {
        $this->count++;
        if (strlen($header) > 2) {
            list($key, $value) = explode(" ", rtrim($header, "\r\n"), 2);
            $key = rtrim($key, ':');
            if(!empty($this->response[$key])) {
                if(is_array($this->response[$key])){
                    $this->response[$key][] = $value;
                } else {
                    $tmp = $this->response[$key];
                    $this->response[$key] = array();
                    $this->response[$key][] = $tmp;
                    $this->response[$key][] = $value;

                }
            } else {
                $this->response[$key] = $value;
            }
        }
        return strlen($header);
    }

    /**
     * Formatting HTTP Response Header
     * 
     */
    protected function request($url, $options = array()){
        // Clean up
        $this->cleanopt();
        // create curl instance
        $curl = curl_init($url);
        // reset before set options
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this,'formatHeader'));

        $this->setopt($options);

        // set headers
        if(empty($this->header)){
            $this->setHeader(array(
                'User-Agent: MoodleBot/1.0',
                'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'Connection: keep-alive'
                ));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);

        // set cookie
        if(!empty($this->cookie)) {
            $this->setopt(array('cookiejar'=>$this->cookie,
                            'cookiefile'=>$this->cookie
                             ));
        }

        // set options
        foreach($this->options as $name => $val) {
            if (is_string($name)) {
                $name = constant(strtoupper($name));
            }
            curl_setopt($curl, $name, $val);
        }

        if($this->debug){
            echo '<h1>Options</h1>';
            var_dump($this->options);
            echo '<h1>Header</h1>';
            var_dump($this->header);
        }


        $ret  = curl_exec($curl);
        $this->info  = curl_getinfo($curl);
        $this->error = curl_error($curl);

        if($this->debug){
            echo '<h1>Return Data</h1>';
            var_dump($ret);
            echo '<h1>Info</h1>';
            var_dump($this->info);
            echo '<h1>Error</h1>';
            var_dump($this->error);
        }

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
        $options['CURLOPT_HTTPGET'] = 0;
        $options['CURLOPT_HEADER']  = 1;
        $options['CURLOPT_NOBODY']  = 1;
        return $this->request($url, $options);
    }

    /**
     * HTTP POST method
     */
    public function post($url, $params = array(), $options = array()){
        $options['CURLOPT_POST']       = 1;
        $options['CURLOPT_POSTFIELDS'] = $params;
        return $this->request($url, $options);
    }

    /**
     * HTTP GET method
     */
    public function get($url, $params = array(), $options = array()){
        $options['CURLOPT_HTTPGET'] = 1;

        if(!empty($params)){
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= http_build_query($params);
        }

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
        $fp   = fopen($file, 'r');
        $size = filesize($file);
        $options['CURLOPT_PUT']        = 1;
        $options['CURLOPT_INFILESIZE'] = $size;
        $options['CURLOPT_INFILE']     = $fp;
        if (!isset($this->options['CURLOPT_USERPWD'])){
            $this->setopt(array('CURLOPT_USERPWD'=>'anonymous: noreply@moodle.org'));
        }
        $ret = $this->request($url, $options);
        fclose($fp);
        return $ret;
    }

    /**
     * HTTP DELETE method
     */
    public function delete($url, $param = array(), $options = array()){
        $options['CURLOPT_CUSTOMREQUEST'] = 'DELETE';
        if (!isset($options['CURLOPT_USERPWD'])) {
            $options['CURLOPT_USERPWD'] = 'anonymous: noreply@moodle.org';
        }
        $ret = $this->request($url, $options);
        return $ret;
    }
    /**
     * HTTP TRACE method
     */
    public function trace($url){
        $options['CURLOPT_CUSTOMREQUEST'] = 'TRACE';
        $ret = $this->request($url, $options);
        return $ret;
    }
    /**
     * HTTP OPTIONS method
     */
    public function options($url){
        $options['CURLOPT_CUSTOMREQUEST'] = 'OPTIONS';
        $ret = $this->request($url, $options);
        return $ret;
    }
}
