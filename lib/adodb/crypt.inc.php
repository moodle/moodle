<?php
//     Session Encryption by Ari Kuorikoski <ari.kuorikoski@finebyte.com>
class MD5Crypt{
        function keyED($txt,$encrypt_key){
                $encrypt_key = md5($encrypt_key);
                $ctr=0;
                $tmp = "";
                for ($i=0;$i<strlen($txt);$i++){
                        if ($ctr==strlen($encrypt_key)) $ctr=0;
                        $tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
                        $ctr++;
                }
                return $tmp;
        }

        function Encrypt($txt,$key){
                srand((double)microtime()*1000000);
                $encrypt_key = md5(rand(0,32000));
                $ctr=0;
                $tmp = "";
                for ($i=0;$i<strlen($txt);$i++)
                {
                if ($ctr==strlen($encrypt_key)) $ctr=0;
                $tmp.= substr($encrypt_key,$ctr,1) .
                (substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1));
                $ctr++;
                }
                return base64_encode($this->keyED($tmp,$key));
        }

        function Decrypt($txt,$key){
                $txt = $this->keyED(base64_decode($txt),$key);
                $tmp = "";
                for ($i=0;$i<strlen($txt);$i++){
                        $md5 = substr($txt,$i,1);
                        $i++;
                        $tmp.= (substr($txt,$i,1) ^ $md5);
                }
                return $tmp;
        }

        function RandPass()
        {
                $randomPassword = "";
                srand((double)microtime()*1000000);
                for($i=0;$i<8;$i++)
                {
                        $randnumber = rand(48,120);

                        while (($randnumber >= 58 && $randnumber <= 64) || ($randnumber >= 91 && $randnumber <= 96))
                        {
                                $randnumber = rand(48,120);
                        }

                        $randomPassword .= chr($randnumber);
                }
                return $randomPassword;
        }

}
?>
