<?php

/**
 * multidomainAdminView class
 * multidomain view class of multidomain module
 * @author largeden (cbrghost@gmail.com)
 * @package /modules/multidomain
 * @version 1.3
 */
class multidomainAdminView extends multidomain
{

	/**
	 * Initilization
	 * @return void
	 */
	function init()
	{
		$template_path = sprintf("%stpl/",$this->module_path);
		$this->setTemplatePath($template_path);
	}

	/**
	 * 멀티 도메인 관리자페이지
	 * @return void
	 */
	function dispMultidomainAdminSetup()
	{
		$args = new stdClass();
		if(Context::get('order_type'))
		{
			$args->order_type = Context::get('order_type');
		}
		else
		{
			$args->order_type = 'desc';
		}
		$args->sort_index = Context::get('sort_index');
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 20;

		$oMultidomainAdminModel = &getAdminModel('multidomain');
		$oMultidomainModel = getModel('multidomain');

		$multidomain = $oMultidomainAdminModel->getMultidomainList($args);

		// 기본 URL
		$db_info = Context::getDBInfo();
		Context::set('default_url', $db_info->default_url);

		// 멀티 도메인 리스트
		Context::set('total_count', $multidomain->total_count);
		Context::set('total_page', $multidomain->total_page);
		Context::set('page', $multidomain->page);
		Context::set('multidomain_list', $multidomain->data);
		Context::set('page_navigation', $multidomain->page_navigation);

		$this->setTemplateFile('list');
	}
}
/* End of file multidomain.admin.view.php */
/* Location: ./modules/multidomain/multidomain.admin.view.php */