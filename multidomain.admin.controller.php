<?php

/**
 * multidomainAdminController class
 * multidomain controller class of multidomain module
 * @author largeden (developer@romanesque.me)
 * @package /modules/multidomain
 * @version 1.4.1
 */
class multidomainAdminController extends multidomain
{

	/**
	 * initialization
	 * @return void
	 */
	function init()
	{
	}

	/**
	 * 멀티 도메인 추가
	 * @return object|void
	 */
	function procMultidomainAdminInsert()
	{
		$oMultidomainModel = &getModel('multidomain');

		$args = new stdClass();
		$args->multidomain_srl = Context::get('multidomain_srl');
		$args->domain = $oMultidomainModel->parseUri(Context::get('domain'));
		$args->module_srl = Context::get('index_module_srl');
		$args->document_srl = Context::get('index_document_srl');

		$oMultidomainModel = &getModel('multidomain');
		if($oMultidomainModel->getMultidomain($args))
		{
			return new Object(-1, "md_msg_domain_exists");
		}
		if($oMultidomainModel->getIsSitesMD($args))
		{
			return new Object(-1, "md_msg_domain_exists2");
		}

		if($args->multidomain_srl)
		{
			$this->updateMultidomain($args);
			$msg_code = "success_updated";
		}
		else
		{
			$this->insertMultidomain($args);
			$msg_code = "success_registed";
		}

		$this->setMessage($msg_code);
	}

	/**
	 * 멀티 도메인 삭제
	 * @return object|void
	 */
	function procMultidomainAdminDelete()
	{
		$args = new stdClass();
		$args->multidomain_srl = Context::get('multidomain_srl');

		$oMultidomainModel = &getModel('multidomain');
		if(!$multidomain_info = $oMultidomainModel->getMultidomainSrl($args->multidomain_srl))
		{
			return new Object(-1, "md_msg_not_domain");
		}

		$args->domain = $multidomain_info->domain;

		$this->deleteMultidomain($args);

		$msg_code = "success_deleted";

		$this->setMessage($msg_code);
	}

	/**
	 * 멀티 도메인 테이블 추가
	 * @return object
	 */
	function insertMultidomain($args) {
		// begin transaction
		$oDB = &DB::getInstance();
		$oDB->begin();

		// trigger 호출 (before) : 타 모듈 연동을 위해 선언
		$output = ModuleHandler::triggerCall('multidomain.insertMultidomain', 'before', $args);
		if(!$output->toBool()) return $output;

		// multidomain_srl 생성
		if(!$args->multidomain_srl) $args->multidomain_srl = getNextSequence();

		$output = executeQuery('multidomain.insertMultidomain', $args);
		if(!$output->toBool()) {
			$oDB->rollback();
			return new Object(-1, "msg_error_occured");
		}

		// trigger 호출 (after) : 타 모듈 연동을 위해 선언
		if($output->toBool()) {
			$trigger_output = ModuleHandler::triggerCall('multidomain.insertMultidomain', 'after', $args);
			if(!$trigger_output->toBool()) {
				$oDB->rollback();
				return $trigger_output;
			}
		}

		// commit
		$oDB->commit();

		// cache를 삭제합니다.
		$this->deleteMultidomainCache('object_getMultidomainList', array($args->domain), 'group');
		$this->deleteMultidomainCache('object_getMultidomainUrl', array($args->domain));

		return $output;
	}

	/**
	 * 멀티 도메인 테이블 수정
	 * @return object
	 */
	function updateMultidomain($args) {
		// begin transaction
		$oDB = &DB::getInstance();
		$oDB->begin();

		// trigger 호출 (before) : 타 모듈 연동을 위해 선언
		$output = ModuleHandler::triggerCall('multidomain.updateMultidomain', 'before', $args);
		if(!$output->toBool()) return $output;

		$output = executeQuery('multidomain.updateMultidomain', $args);
		debugPrint($output);
		if(!$output->toBool()) {
			$oDB->rollback();
			return new Object(-1, "msg_error_occured");
		}

		// trigger 호출 (after) : 타 모듈 연동을 위해 선언
		if($output->toBool()) {
			$trigger_output = ModuleHandler::triggerCall('multidomain.updateMultidomain', 'after', $args);
			if(!$trigger_output->toBool()) {
				$oDB->rollback();
				return $trigger_output;
			}
		}

		// commit
		$oDB->commit();

		// cache를 삭제합니다.
		$this->deleteMultidomainCache('object_getMultidomainList', array($args->domain), 'group');
		$this->deleteMultidomainCache('object_getMultidomainUrl', array($args->domain));

		return $output;
	}

	/**
	 * 멀티 도메인 테이블 삭제
	 * @return object
	 */
	function deleteMultidomain($args) {
		// begin transaction
		$oDB = &DB::getInstance();
		$oDB->begin();

		// trigger 호출 (before) : 타 모듈 연동을 위해 선언
		$output = ModuleHandler::triggerCall('multidomain.deleteMultidomain', 'before', $args);
		if(!$output->toBool()) return $output;

		$output = executeQuery('multidomain.deleteMultidomain', $args);
		if(!$output->toBool()) {
			$oDB->rollback();
			return new Object(-1, "msg_error_occured");
		}

		// trigger 호출 (after) : 타 모듈 연동을 위해 선언
		if($output->toBool()) {
			$trigger_output = ModuleHandler::triggerCall('multidomain.deleteMultidomain', 'after', $args);
			if(!$trigger_output->toBool()) {
				$oDB->rollback();
				return $trigger_output;
			}
		}

		// commit
		$oDB->commit();

		// cache를 삭제합니다.
		$this->deleteMultidomainCache('object_getMultidomainList', array($args->domain), 'group');
		$this->deleteMultidomainCache('object_getMultidomainUrl', array($args->domain));
		
		return $output;
	}
}
/* End of file multidomain.admin.controller.php */
/* Location: ./modules/multidomain/multidomain.admin.controller.php */