<?php
$con = mysql_connect("localhost","账号","密码");
$db  = mysql_select_db("数据库",$con);
mysql_query("set names gbk");
define("TOKEN", "这个公众平台号");
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
                                 //加载图文模版
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
                	$contentStr = "欢迎关注微度生活，支持365日租房的查询，输入相关地区、学校即可查询";
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
						$contentStr = "很抱歉没有搜索到相关信息，您可以登录http://m.365rzf.com来查询相关信息" ;
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