<?php

/**
 * multidomain class
 * Base class of multidomain module
 * @author largeden (developer@romanesque.me)
 * @package /modules/multidomain
 * @version 1.4
 */
class multidomain extends ModuleObject
{

	/**
	 * Install multidomain module
	 * @return Object
	 */
	function moduleInstall()
	{
		$oModuleController = &getController('module');
		$oModuleController->insertTrigger('moduleObject.proc', 'multidomain', 'model', 'triggerMultidomain', 'before');
		$oModuleController->insertTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before');

		return new Object();
	}

	/**
	 * If update is necessary it returns true
	 * @return bool
	 */
	function checkUpdate()
	{
		$oModuleModel = &getModel('module');
		if(!$oModuleModel->getTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before'))
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Update module
	 * @return Object
	 */
	function moduleUpdate()
	{
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');
		if(!$oModuleModel->getTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before'))
		{
			$oModuleController->insertTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before');
		}

		return new Object();
	}

	/**
	 * Regenerate cache file
	 * @return void
	 */
	function recompileCache()
	{
	}

	/**
	 * Module deleted
	 * @return Object
	 */
	function moduleUninstall()
	{
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');
		$oDB = &DB::getInstance();

		// Trigger Delete
		if($oModuleModel->getTrigger('moduleObject.proc', 'multidomain', 'model', 'triggerMultidomain', 'before'))
			$oModuleController->deleteTrigger('moduleObject.proc', 'multidomain', 'model', 'triggerMultidomain', 'before');
		if($oModuleModel->getTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before'))
			$oModuleController->deleteTrigger('moduleHandler.init', 'multidomain', 'model', 'triggerMultidomainInit', 'before');

		// Table Delete
		$table_list = array(
			'multidomain'
		);

		foreach($table_list as $table_name)
		{
			if($oDB->isTableExists($table_name))
			{
				$oDB->begin();
				$result = $oDB->_query(sprintf("drop table %s%s", $oDB->prefix, $table_name));
				if($oDB->isError()) { $oDB->rollback(); return; }
			}
		}

		// commit
		$oDB->commit();
		return new Object();
	}

	/**
	 * Set the multidomain cache
	 * @return bool
	 */
	function setMultidomainCache($name = 'object_multidomain', $args = NULL, $cache = NULL, $group = NULL)
	{
		if($args == NULL)
		{
			return false;
		}

		// cache controll
		$oCacheHandler = &CacheHandler::getInstance('object');

		// cache
		if($oCacheHandler->isSupport())
		{
			$keys = '';
			foreach($args as $key => $val)
			{
				$keys .= "_{$key}:{$val}";
			}

			// object_memorize:_board_234324 와 같은 키 이름을 구합니다.
			if($group)
			{
				$cache_key = $oCacheHandler->getGroupKey($name, "{$name}:{$keys}");
			}
			else
			{
				$cache_key = "{$name}:{$keys}";
			}

			$oCacheHandler->put($cache_key, $cache);

			return true;
		}

		return false;
	}

	/**
	 * Return the multidomain cache
	 * @return bool
	 */
	function getMultidomainCache($name = 'object_multidomain', $args = NULL, $group = NULL)
	{
		if($args == NULL)
		{
			return false;
		}

		// cache controll
		$oCacheHandler = &CacheHandler::getInstance('object');

		// cache
		if($oCacheHandler->isSupport())
		{
			$keys = '';
			foreach($args as $key => $val)
			{
				$keys .= "_{$key}:{$val}";
			}

			// object_memorize:_board_234324 와 같은 키 이름을 구합니다.
			if($group)
			{
				$cache_key = $oCacheHandler->getGroupKey($name, "{$name}:{$keys}");
			}
			else
			{
				$cache_key = "{$name}:{$keys}";
			}

			$cache = $oCacheHandler->get($cache_key);

			return $cache;
		}

		return false;
	}

	/**
	 * Delete the multidomain cache
	 * @return void
	 */
	function deleteMultidomainCache($name = 'object_multidomain', $args = NULL, $group = NULL)
	{
		if($args == NULL)
		{
			return false;
		}

		// cache controll
		$oCacheHandler = &CacheHandler::getInstance('object');

		if($oCacheHandler->isSupport())
		{
			if($group)
			{
				$oCacheHandler->invalidateGroupKey($name);
			}
			else
			{
				$keys = '';
				foreach($args as $key => $val)
				{
					$keys .= "_{$key}:{$val}";
				}

				$cache_key = "{$name}:{$keys}";
				$oCacheHandler->delete($cache_key);
			}
		}
	}
}
/* End of file multidomain.class.php */
/* Location: ./modules/multidomain/multidomain.class.php */