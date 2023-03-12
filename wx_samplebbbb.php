<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=" />
<title>Ventuz，微信链接测试!</title>
</head>

<body>


<?php
	header("content-Type: text/html; charset=Utf-8"); 
	//链接Mysql
	$link = mysql_connect("test.rzx.me" , "root" , "27****zu");

	if(!$link){
		echo "与数据库链接失败；";
		exit;
	}
	//选择数据库
	mysql_select_db("ventuz");
?>



<!--html页面显示部分-->
<div style="width:500px; background:#c60;margin:50px auto;">
<?php
	$sql="select name from test";
	$result=mysql_query($sql);
	var_dump($result);
	while(list($name)=mysql_fetch_row($result)){
		echo '<div style="margin:10px auto; font-size:12px; text-align:center; color:#fff;">'.$name.'</div>';
		}
?>

</div>



<?php
/*
----------------------------------
Ventuz链接微信，通过数据库交换数据
微信接口--部分
----------------------------------
*/
define("TOKEN", "jcweb123456");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
		//	$keyword = trim($postObj->MsgType);
		
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";		
            if($keyword == "vvv")
            {
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
			else if($keyword == "bbb"){
				$msgType = "text";
                $contentStr = hhh;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
			}
			else if($keyword == "hi"){
				$sql="select name from city";
				$msgType = "text";
                $contentStr = 你也好;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
			}
			else{
				$msgType = "text";
                $contentStr = $keyword;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
			}
			
			
			//数据库写入信息
			$sql="insert into test(name,time) values('$contentStr','$time')";
			$result=mysql_query($sql);
			mysql_close();

			
        }else{
            echo "";
            exit;
        }
    }
}
?>






</body>
</html>

