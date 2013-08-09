<?php
$con = mysql_connect("test.rzx.me","root","2735xuezu");
$db  = mysql_select_db("fucktest",$con);
mysql_query("set names utf8");
define("TOKEN", "jcweb123456");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $type = $postObj->MsgType;
				$event = $postObj->Event;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>"; 
                                 //加载图文模版!
				$picTpl = "<xml>
								 <ToUserName><![CDATA[%s]]></ToUserName>
								 <FromUserName><![CDATA[%s]]></FromUserName>
								 <CreateTime>%s</CreateTime>
								 <MsgType><![CDATA[%s]]></MsgType>
								 <ArticleCount>1</ArticleCount>
								 <Articles>
								 <item>
								 <Title><![CDATA[%s]]></Title> 
								 <Description><![CDATA[%s]]></Description>
								 <PicUrl><![CDATA[%s]]></PicUrl>
								 <Url><![CDATA[%s]]></Url>
								 </item>
								 </Articles>
								 <FuncFlag>1</FuncFlag>
							</xml> ";
				if($type == "event" && $event == "subscribe")
                {
              		$msgType = "text";
                	$contentStr = "欢迎关注-目前Ventuz-测试阶段";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
					$keywords = iconv("UTF-8","gbk",$keyword);
					$result = mysql_query("select aid,typeid from `数据库` where address like '%{$keywords}%' or xuexiao like '%{$keywords}%' LIMIT 1");
					while ($row = mysql_fetch_array($result)) {
						$id = $row['aid'];
						$typeid = $row['typeid'];
					}
					if(empty($id)){
						$msgType = "text";
						$contentStr = "没有搜索到相关信息，请更换关键词再试" ;
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						echo $resultStr;
					}else{
						$url = array(
							1 => 'http://www.365rzf.com/rizu/',
							2 => 'http://www.365rzf.com/hour/',
							3 => 'http://www.365rzf.com/yuezu/',
							4 => 'http://www.365rzf.com/news/',
							5 => 'http://www.365rzf.com/student/',
							6 => 'http://www.365rzf.com/lvyou/',
							7 => 'http://www.365rzf.com/shop/'
						);
						$xinxi = mysql_query("select title,litpic,pubdate,description from `数据库` where id ={$id}");
						while ($jay = mysql_fetch_array($xinxi)) {
							$title = $jay['title'];
							$image = "http://www.365rzf.com".$jay['litpic'];
							$data  = date('Y/md',$jay['pubdate']);
							$desription = $jay['description'];
						}
						$turl = $url[$typeid].$data."/".$id.".html";    //url抵制
						$title= iconv("GBK","UTF-8",$title);            //标题名称
						$desription= iconv("GBK","UTF-8",$desription);  //描述
						$msgType = "news"; 								//类型
						$resultStr = sprintf($picTpl, $fromUsername, $toUsername, $time, $msgType, $title,$desription,$image,$turl);
						echo $resultStr;
					}
                }
        }else {
        	echo "";
        	exit;
        }
    }
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
}

?>