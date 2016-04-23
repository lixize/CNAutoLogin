<?php
class cycn
{
    protected $cookie, $loginUrl, $logoutUrl, $imgUrl;
    public $rand, $ip;
    public function __construct()
    {
        $this->loginUrl = 'http://enet.10000.gd.cn:10001/login.do'; //登陆网址
        $this->logoutUrl = 'http://enet.10000.gd.cn:10001/logout.do'; //取消登陆网址
        $this->imgUrl = 'http://enet.10000.gd.cn:10001/common/image.jsp'; //验证码网址
    }

    public function getIP()
    {
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $ip = gethostbyname("$hostname");
        return $ip;
    }

    public function curl_request($url, $post_data = array())
    {
        if(!$url) return false;
        $ch = curl_init();  //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        if($post_data)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }
        if($this->cookie)
        {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        list($header, $body) = explode("\r\n\r\n", $data, 2);//分离header和body
        if(!$this->cookie)
        {
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            if(isset($matches[1][0]))
            {
                $this->cookie = substr($matches[1][0], 1);
            }
        }
        return $body;
    }

    public function getValidateCode()
    {
        $data = $this->curl_request($this->imgUrl);
        //将验证码保存到本地，核对使用
        $file = fopen('validatecode.jpeg', 'w'); 
        fwrite($file, $data);
        fclose($file);
        //识别验证码
        include_once ('Valite.php');
        $valite = new Valite();
        $valite->setImage('validatecode.jpeg');
        $valite->getHec();
        $ert = $valite->run();
        //echo "验证码：".$ert;
        return $ert;
    }

    public function login($user, $passwd)
    {
        $this->rand = $this->getValidateCode();
        $this->ip = $this->getIP();
        $post_data = array(
            'userName1' => $user,           //账号
            'password1' => $passwd,         //密码
            'rand'      => $this->rand,     //验证码
            'eduuser'   => $this->ip,       //用户的ip
            'edubas'    => '113.98.10.136', //服务器的ip
        );
        $data = $this->curl_request($this->loginUrl, $post_data); 
        //判断是否登陆成功
        preg_match_all('/<div id="(.*?)">(.*?)<\/div>/is', $data, $matches);
        if(isset($matches[1][0]))
        {
            if($matches[1][0] == 'success')
                return true;
            else
                return $matches[2][0];
        }
        return false;
    }

    public function logout()
    {
        $post_data = array(
            'eduuser'   => $this->ip,       //用户的ip
            'edubas'    => '113.98.10.136', //服务器的ip
        );
        $data = $this->curl_request($this->loginUrl, $post_data); 
        //判断是否取消登陆成功
        preg_match_all('/<div id="(.*?)">(.*?)<\/div>/is', $data, $matches);
        if(isset($matches[2][0]))
        {
            return $matches[2][0];
        }
        return false;
    }

}

//$url = 'http://enet.10000.gd.cn:10001/login.jsp?wlanuserip='.$ip.'&wlanacip=113.98.10.136';
$user = 'xxxxxx';//在此处输入用户名和密码
$password = 'xxxxxxxx';
$obj = new cycn();
$result = $obj->login($user, $password); 
if($result === true)
    $status = "登陆成功";
else
    $status = $result;
$randcode = $obj->rand;
$mloginurl = 'http://enet.10000.gd.cn:10001/login.jsp?wlanuserip='.$obj->ip.'&wlanacip=113.98.10.136';
require('view.php');