<?php
	/**
	 *  阿里云api网关连接基类
	 * Created by Wangqs
	 * Date: 2021/3/22 12:03
	 */

	use Wangqs\AliApiGetway\Constant\ContentType;
	use Wangqs\AliApiGetway\Constant\HttpHeader;
	use Wangqs\AliApiGetway\Constant\HttpMethod;
	use Wangqs\AliApiGetway\Constant\SystemHeader;
	use Wangqs\AliApiGetway\Http\HttpClient;
	use \Wangqs\AliApiGetway\Http\HttpRequest;

	class Demo
	{

		private $appKey = "your appKey";
		private $appSecret = "your appSecret";

		private $host = "your host need http or https";

		public function __construct ( $appKey , $appSecret ,$host) {
			$this->appKey = $appKey;
			$this->appSecret = $appSecret;
			$this->host = $host;
		}

		protected function request($path,$method){
			return new HttpRequest($this->host,$path,$method, $this->appKey , $this->appSecret);
		}

		/**
		 * @author     :  Wangqs  2021/3/22
		 * @description:  get 请求
		 */
		public function doGet ( $path , array $headers , array $querys , $debug = false ) {
			//域名后、query前的部分
			$request = $this->request( $path , HttpMethod::GET);

			//设定Content-Type，根据服务器端接受的值来设置
			$request->setHeader( HttpHeader::HTTP_HEADER_CONTENT_TYPE , ContentType::CONTENT_TYPE_TEXT );

			//设定Accept，根据服务器端接受的值来设置
			$request->setHeader( HttpHeader::HTTP_HEADER_ACCEPT , ContentType::CONTENT_TYPE_TEXT );
			//如果是调用测试环境请设置
			$debug && $request->setHeader( SystemHeader::X_CA_STAG , "TEST" );


			//注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
			//mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
			//指定参与签名的header
			$request->setSignHeader( SystemHeader::X_CA_TIMESTAMP );
			if ( is_array( $headers ) ) {
				foreach ( $headers as $key => $node ) {
					$request->setHeader( $key , $node );
					$request->setSignHeader( $key );
				}
			}

			//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
			if ( (is_array( $querys )) ) {
				foreach ( $querys as $key => $node ) {
					$request->setQuery( $key , $node );
				}
			}

			return HttpClient::execute( $request );
		}


		/**
		 * @author     :  Wangqs  2021/3/22
		 * @description:  POST 表单请求
		 */
		public function doPostForm ( $path , $headers , $querys , $bodys , $debug = false ) {
			//域名后、query前的部分
			$request = $this->request( $path , HttpMethod::POST);

			//设定Content-Type，根据服务器端接受的值来设置
			$request->setHeader( HttpHeader::HTTP_HEADER_CONTENT_TYPE , ContentType::CONTENT_TYPE_FORM );

			//设定Accept，根据服务器端接受的值来设置
			$request->setHeader( HttpHeader::HTTP_HEADER_ACCEPT , ContentType::CONTENT_TYPE_JSON );
			//如果是调用测试环境请设置
			$debug && $request->setHeader( SystemHeader::X_CA_STAG , "TEST" );


			//注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
			//mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
			//同时指定参与签名的header
			$request->setSignHeader( SystemHeader::X_CA_TIMESTAMP );
			if ( is_array( $headers ) ) {
				foreach ( $headers as $key => $node ) {
					$request->setHeader( $key , $node );
					$request->setSignHeader( $key );
				}
			}

			//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
			if ( (is_array( $querys )) ) {
				foreach ( $querys as $key => $node ) {
					$request->setQuery( $key , $node );
				}
			}

			//注意：业务body部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
			if ( (is_array( $bodys )) ) {
				foreach ( $bodys as $key => $node ) {
					$request->setBody( $key , $node );
				}
			}

			return HttpClient::execute( $request );
		}


		/**
		 * @author     :  Wangqs  2021/3/22
		 * @description:    POST  非表单请求 String
		 */
		public function doPostString($path, array $headers, array $querys, $bodyContent, $debug=false) {
			//域名后、query前的部分
			$request = $this->request( $path , HttpMethod::POST);
			//传入内容是json格式的字符串
			// $bodyContent = "{\"inputs\": [{\"image\": {\"dataType\": 50,\"dataValue\": \"base64_image_string(此行)\"},\"configure\": {\"dataType\": 50,\"dataValue\": \"{\"side\":\"face(#此行此行)\"}\"}}]}";

			//设定Content-Type，根据服务器端接受的值来设置
			$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_JSON);

			//设定Accept，根据服务器端接受的值来设置
			$request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
			//如果是调用测试环境请设置
			$debug && $request->setHeader(SystemHeader::X_CA_STAG, "TEST");


			//注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
			//mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
			$request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
			if (is_array($headers)){
				foreach ($headers as $key=>$node){
					$request->setHeader($key, $node);
					$request->setSignHeader($key);
				}
			}

			//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
			if ((is_array($querys))){
				foreach ($querys as $key=>$node){
					$request->setQuery($key, $node);
				}
			}

			//注意：业务body部分，不能设置key值，只能有value
			if (strlen($bodyContent) > 0) {
				$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_MD5, base64_encode(md5($bodyContent, true)));
				$request->setBodyString($bodyContent);
			}


			return HttpClient::execute($request);
		}

		/**
		 * @author     :  Wangqs  2021/3/22
		 * @description:  POST  非表单请求 Stream
		 */
		public function doPostStream($path, array $headers, array $querys, array $bytes, $bodyContent, $debug=false) {
			//域名后、query前的部分
			// $path = "/poststream";
			$request = $this->request( $path , HttpMethod::POST);
			//Stream的内容
			// $bytes = array();
			//传入内容是json格式的字符串
			// $bodyContent = "{\"inputs\": [{\"image\": {\"dataType\": 50,\"dataValue\": \"base64_image_string(此行)\"},\"configure\": {\"dataType\": 50,\"dataValue\": \"{\"side\":\"face(#此行此行)\"}\"}}]}";

			//设定Content-Type，根据服务器端接受的值来设置
			$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_STREAM);

			//设定Accept，根据服务器端接受的值来设置
			$request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_JSON);
			//如果是调用测试环境请设置
			$debug && $request->setHeader(SystemHeader::X_CA_STAG, "TEST");


			//注意：业务header部分，如果没有则无此行(如果有中文，请做Utf8ToIso88591处理)
			//mb_convert_encoding("headervalue2中文", "ISO-8859-1", "UTF-8");
			$request->setSignHeader(SystemHeader::X_CA_TIMESTAMP);
			if (is_array($headers)){
				foreach ($headers as $key=>$node){
					$request->setHeader($key, $node);
					$request->setSignHeader($key);
				}
			}

			//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
			if ((is_array($querys))){
				foreach ($querys as $key=>$node){
					$request->setQuery($key, $node);
				}
			}

			//注意：业务body部分，不能设置key值，只能有value
			if (is_array($bytes)){
				foreach($bytes as $byte) {
					$bodyContent .= chr($byte);
				}
			}

			if (0 < strlen($bodyContent)) {
				$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_MD5, base64_encode(md5($bodyContent, true)));
				$request->setBodyStream($bodyContent);
			}

			return HttpClient::execute($request);
		}

	}