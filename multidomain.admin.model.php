<?php

/**
 * multidomainAdminModel class
 * multidomain model class of multidomain module
 * @author largeden (cbrghost@gmail.com)
 * @package /modules/multidomain
 * @version 1.3
 */
class multidomainAdminModel extends multidomain
{

	/**
	 * 멀티 도메인 리스트(관리자용)
	 * @param array $args
	 * @param array $columnList
	 * @return object
	 */
	function getMultidomainList($args = null, $columnList = array())
	{
		// cache를 불러옵니다.
		if($cache = $this->getMultidomainCache('object_getMultidomainList', $args, 'group'))
		{
			return $cache;
		}

		$oModuleModel = &getModel('module');

		$output = executeQuery('multidomain.getMultidomainList', $args, $columnList);
		if(!$output->toBool()||!$output->data) return;

		if(!is_array($output->data)) $output->data = array($output->data);

		foreach($output->data as $key => $val)
		{
			$output->data[$key] = $val;
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($val->module_srl);
			$output->data[$key]->browser_title = $module_info->browser_title;
			$output->data[$key]->mid = $module_info->mid;
		}

		// cache를 저장합니다.
		$this->setMultidomainCache('object_getMultidomainList', $args, $output, 'group');

		return $output;
	}
}
/* End of file multidomain.admin.model.php */
/* Location: ./modules/multidomain/multidomain.admin.model.php */