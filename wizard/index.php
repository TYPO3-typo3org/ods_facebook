<?php
$BACK_PATH = '../../../../typo3/';
define('TYPO3_MOD_PATH', '../typo3conf/ext/ods_facebook/wizard/');
$MCONF['name']='xMOD_tx_odsfacebook_wizard';
$MCONF['access']='user,group';
$MCONF['script']='index.php';

require_once ($BACK_PATH.'init.php');
require_once ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:ods_facebook/wizard/locallang.xml');

require_once(PATH_t3lib."class.t3lib_scbase.php");
require_once(t3lib_extMgm::extPath('ods_facebook').'class.tx_odsfacebook_div.php');

class tx_odsfacebook_wizard extends t3lib_SCbase {
	// Internal, static: GPvars
	var $P; // Wizard parameters, coming from TCEforms linking to the wizard.
	var $config;

	/**
	* Main function of the module. Write the content to $this->content
	*
	* @return   the wizard
	*/
	function main(){
		global $BE_USER,$BACK_PATH;

		// Draw the header.
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;

		// GPvars:
		$this->P=t3lib_div::_GP('P');
			
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id) || ($BE_USER->user["uid"] && !$this->id))    {
			if (($BE_USER->user['admin'] && !$this->id) || ($BE_USER->user["uid"] && !$this->id)) {
				$this->moduleContent();
			}
		}
	}

	/**
	* Outputting the accumulated content to screen
	*
	* @return	void
	*/
	function printContent(){
		echo $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		echo $this->content;
		echo $this->doc->endPage();
	}

  function moduleContent(){
		global $LANG;

		if($this->P){
			$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_odsfacebook_auth','uid='.intval($this->P['uid']));
			$auth=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$fb=new tx_odsfacebook_div($auth);
		}

		if($_GET['session']){
			$session=json_decode(stripslashes($_GET['session']));
			$fb->config['session_key']=$session->session_key;
			$access_token=$fb->getUserAccessToken();
			$js="
window.opener.document.editform['".strtr($this->P['itemName'],array('_hr'=>''))."'].value='".$access_token."';
window.opener.document.editform['".$this->P['itemName']."'].value='".$access_token."';
window.opener.".$this->P['fieldChangeFunc']['TBE_EDITOR_fieldChanged']."
window.close();";
		}else{
			$url=$fb->getLoginLink(t3lib_div::locationHeaderUrl($_SERVER['SCRIPT_NAME'].'?'.http_build_query(array('P'=>$this->P))));
			header('Location: '.$url);
			exit();
		}

		$this->content.='<script type="text/javascript">'.$js.'</script>';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_facebook/wizard/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_facebook/wizard/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_odsfacebook_wizard');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>
