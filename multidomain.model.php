<?php

/**
 * multidomainModel class
 * multidomain model class of multidomain module
 * @author largeden (cbrghost@gmail.com)
 * @package /modules/multidomain
 * @version 1.3
 */
class multidomainModel extends multidomain
{
	/**
	 * @brief initialization
	 **/
	function init() {
	}

	/**
	 * Setup the initialization multidomain
	 * @param stdClass $module_info initialization information
	 * @return void
	 */
	function triggerMultidomainInit(&$module_info)
	{
		// Form return 처리에 도메인 이름까지 들어올 경우 '기본 URL 설정이 안 되어 있습니다.'라며 접속이 안되는 증상 회피기능 추가
		if(Context::get('success_return_url') || Context::get('error_return_url'))
		{
			$this->getMultidomainReturn();
		}

		if(Context::get('document_srl') || Context::get('mid') || Context::get('module') || Context::get('module_srl'))
		{
			return;
		}

		// 멀티 도메인 수행
		$multidomain = $this->getMultidomainURL();

		// 모듈명이 존재하면 기본 mid를 변경함
		if($multidomain->mid)
		{
			$module_info->mid = $multidomain->mid;
			Context::set('mid', $multidomain->mid);
		}

		// 문서번호가 존재하면 document_srl를 변경함
		if($multidomain->document_srl > 0)
		{
			$this->document_srl = $multidomain->document_srl;
			Context::set('document_srl', $multidomain->document_srl);
		}
	}

	/**
	 * Setup the default domain
	 * @return void
	 */
	function triggerMultidomain()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			return FALSE;
		}

		$args = new stdClass();
		$args->domain = $this->parseUri($_SERVER["HTTP_REFERER"]);
		$multidomain_info = $this->getMultidomain($args);

		$this->defaultUrl($multidomain_info->domain);
	}

	/**
	 * 멀티 도메인 체크
	 * @param int $multidomain_srl
	 * @param array $columnList
	 * @return object
	 */
	function getMultidomainSrl($multidomain_srl = null, $columnList = array())
	{
		if(!$multidomain_srl)
		{
			return;
		}

		$args = new stdClass();
		$args->multidomain_srl = $multidomain_srl;
		$output = executeQuery('multidomain.getMultidomainSrl', $args, $columnList);
		if(!$output->toBool()||!$output->data) return;

		return $output->data;
	}

	/**
	 * 멀티 도메인 상세 정보보기
	 * @param array $args
	 * @param array $columnList
	 * @return object
	 */
	function getMultidomain($args = array(), $columnList = array())
	{
		// cache를 불러옵니다.
		if($cache = $this->getMultidomainCache('object_getMultidomain', array($args->domain)))
		{
			return $cache;
		}

		$output = executeQuery('multidomain.getMultidomain', $args, $columnList);
		if(!$output->toBool()||!$output->data) return;

		// cache를 저장합니다.
		$this->setMultidomainCache('object_getMultidomain', array($args->domain), $output->data);

		return $output->data;
	}

	/**
	 * mid, document_srl 구하기
	 * @return object
	 */
	function getMultidomainURL()
	{
		if(Context::getDefaultUrl())
		{
			return;
		}

		$args = new stdClass();
		$args->domain = $this->parseUri(Context::get('request_uri'));

		// cache를 불러옵니다.
		if($cache = $this->getMultidomainCache('object_getMultidomainUrl', array($args->domain)))
		{
			return $cache;
		}

		$output = executeQuery('multidomain.getMultidomainUrl', $args);
		if(!$output->toBool()||!$output->data) return;

		// cache를 저장합니다.
		$this->setMultidomainCache('object_getMultidomainUrl', array($args->domain), $output->data);

		return $output->data;
	}

	/**
	 * HOST 정보얻기
	 * @return string
	 */
	function parseUri($uri = NULL)
	{
		if($uri == NULL)
		{
			return;
		}

		if(!preg_match("/^(http|https|tcp|udp|ssl|vls):\/\//", $uri))
		{
			$uri = "http://{$uri}";
		}
		$uri = @parse_url($uri);

		if(!$uri['host'])
		{
			return;
		}

		$host = $uri['scheme'].'://'.$uri['host'];
		if($uri['port'])
		{
			$host .= ':'.$uri['port'];
		}

		return $host;
	}

	/**
	 * 카페, 텍스타일의 가상도메인 검사(관리자용)
	 * @param array $args
	 * @param array $columnList
	 * @return object
	 */
	function getIsSitesMD($args = null, $columnList = array())
	{
		$uri = $this->parseUri($args->domain);

		$obj = new stdClass();
		$obj->domain = $uri['host'];
		$output = executeQuery('multidomain.getIsSitesMD', $obj, $columnList);
		if(!$output->toBool()||!$output->data) return;

		return $output->data;
	}

	/**
	 * 기본 도메인 생성
	 * @param bool $url
	 * @return void
	 */
	function defaultUrl($url = NULL)
	{
		if($url == NULL)
		{
			return;
		}

		$domain = Context::getInstance();
		$domain->db_info->default_url = $url;
	}

	/**
	 * 접근 안되는 증상 회피
	 * @return void
	 */
	function getMultidomainReturn()
	{
		$urls = array(Context::get('success_return_url'), Context::get('error_return_url'));
		foreach($urls as $url)
		{
			if(empty($url))
			{
				continue;
			}

			$obj = new stdClass();
			$obj->domain = $this->parseUri($url);
			if(!$obj->domain)
			{
				continue;
			}

			if($multidomain_info = $this->getMultidomain($obj))
			{
				$this->defaultUrl($obj->domain);
				return;
			}

			if($multidomain_site_info = $this->getIsSitesMD($obj))
			{
				$this->defaultUrl($obj->domain);
				return;
			}
		}
	}
}
/* End of file multidomain.model.php */
/* Location: ./modules/multidomain/multidomain.model.php */