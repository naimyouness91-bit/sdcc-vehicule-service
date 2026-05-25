<?php
$base='http://127.0.0.1:8000';
$cookie=__DIR__.'/cookies_access.txt'; @unlink($cookie);
function curl_get($url,$cookie){$ch=curl_init($url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie);curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);curl_setopt($ch,CURLOPT_USERAGENT,'SimClient/1.0');$res=curl_exec($ch);$info=curl_getinfo($ch);curl_close($ch);return['body'=>$res,'info'=>$info];}
function curl_post($url,$data,$cookie){$ch=curl_init($url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_POST,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$data);curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie);curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);curl_setopt($ch,CURLOPT_USERAGENT,'SimClient/1.0');$res=curl_exec($ch);$info=curl_getinfo($ch);curl_close($ch);return['body'=>$res,'info'=>$info];}
// get login csrf
$r=curl_get($base.'/login',$cookie);
if(!preg_match('/name="_token" value="([^"]+)"/',$r['body'],$m)){echo "no token\n";exit(1);} $token=$m[1];
$login=['_token'=>$token,'email'=>'superadmin@sdcc.ma','password'=>'ChangeMe@123456'];
$lp=curl_post($base.'/login',$login,$cookie);
echo "Login HTTP: ".$lp['info']['http_code']." url=".$lp['info']['url']."\n";
$u=curl_get($base.'/utilisateurs',$cookie);
echo "GET /utilisateurs HTTP: ".$u['info']['http_code']." final_url=".$u['info']['url']."\n";
if($u['info']['http_code']===200){echo substr($u['body'],0,600);}
?>