<?php

class Emoncms
{
    private $connect_timeout = 5;
    private $total_timeout = 10;

    public $host = "http://emoncms.org";
    
    public function __construct()
    {
    
    }
    
    public function user_auth($username,$password)
    {
       return json_decode($this->request("POST",$this->host."/user/auth.json","username=$username&password=$password")); 
    }
    
    public function user_register($username,$email,$password)
    {
       return json_decode($this->request("POST",$this->host."/user/register.json","username=$username&email=$email&password=$password")); 
    }
    
    public function user_get($apikey)
    {
       return json_decode($this->request("GET",$this->host."/user/get.json?apikey=$apikey",null)); 
    }

    public function feed_getmeta($id,$apikey)
    {
        return file_get_contents($this->host."/feed/getmeta.json?id=$id&apikey=".$apikey);
    }
        
    public function feed_list($apikey)
    {
        return file_get_contents($this->host."/feed/list.json?apikey=".$apikey);
    }
    
    public function feed_data($id,$start,$end,$interval,$skipmissing,$limitinterval,$apikey) 
    {
        return file_get_contents($this->host."/feed/data.json?id=$id&start=$start&end=$end&interval=$interval&skipmissing=$skipmissing&limitinterval=$limitinterval&apikey=$apikey");
    }
    
    public function feed_data_DMY($id,$start,$end,$mode,$apikey) 
    {
        return file_get_contents($this->host."/feed/data.json?id=$id&start=$start&end=$end&mode=$mode&apikey=$apikey");
    }
    
    private function request($method,$url,$body)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if ($body!=null) curl_setopt($curl, CURLOPT_POSTFIELDS,$body);
        
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,$this->connect_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT,$this->total_timeout);
        
        $curl_response = curl_exec($curl);
        curl_close($curl);
        return $curl_response;
    }
    
}
