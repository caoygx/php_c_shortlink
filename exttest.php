<?php
//dl("imgurl.so");

$url = "http://www.qunimabi.com/wo/cao/ni/ma/bi/index.php";

$ret = array();
$st = gettimeofday(1);
for($i = 0; $i < 1; $i++){
    $ret[] = imgurl($url."?sb=$i");
}
echo "ext_time=",gettimeofday(1) - $st;

var_dump($ret);
echo "\r\n\r\n\r\n";

class Short_Url {
        #字符表
        public static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_-";

        public static function short($url) {
            $key = "alexis";
            $urlhash = md5($url);
            $len = strlen($urlhash);
echo "php_md5 = $urlhash";
            #将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
            for ($i = 0; $i < 4; $i++) {
                $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
                #将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
                $hex = hexdec($urlhash_piece) & 0x3fffffff; #此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常

                $short_url = "http://t.cn/";
                #生成6位短连接
                for ($j = 0; $j < 6; $j++) {
                    #将得到的值与0x0000003d,3d为61，即charset的坐标最大值
                    $short_url .= self::$charset[$hex & 0x0000003d];
                    #循环完以后将hex右移5位
                    $hex = $hex >> 5;
                }

                $short_url_list[] = $short_url;
            }

            return $short_url_list;
        }
    }

//    $url = "http://www.cnblogs.com/zemliu/";
//  $short = Short_Url::short($url);
  //  print_r($short);

$ret = array();
$st = gettimeofday(1);
for($i = 0; $i < 1; $i++){
    $ret[] = Short_Url::short($url."?sb=$i");
}

echo "php_time=",gettimeofday(1) - $st;
echo "\n\n";
var_dump($ret);
?>
