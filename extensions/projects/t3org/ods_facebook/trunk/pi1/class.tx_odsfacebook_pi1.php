<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Robert Heel <rheel@1drop.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('ods_facebook').'class.tx_odsfacebook_div.php');
require_once(t3lib_extMgm::extPath('static_info_tables').'pi1/class.tx_staticinfotables_pi1.php');


/**
 * Plugin 'Facebook' for the 'ods_facebook' extension.
 *
 * @author	Robert Heel <rheel@1drop.de>
 * @package	TYPO3
 * @subpackage	tx_odsfacebook
 */
class tx_odsfacebook_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_odsfacebook_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_odsfacebook_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ods_facebook';	// The extension key.
	var $pi_checkCHash = true;

	var $config;
	
	function init($conf){
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

		$flex=array();
		$options=array('auth','api_call','filter_user','num');
		foreach($options as $option){
			$value=$this->pi_getFFvalue($this->cObj->data['pi_flexform'],$option,'sDEF');
			if($value) $flex[$option]=$value;
		}

		$this->config=array_merge($conf,$flex);
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);

		$staticInfoObj = &t3lib_div::getUserObj('&tx_staticinfotables_pi1');
		if ($staticInfoObj->needsInit()){
			$staticInfoObj->init();
		}
		$language=$staticInfoObj->getStaticInfoName('LANGUAGES',$GLOBALS['TSFE']->lang=='default' ? 'en' : $GLOBALS['TSFE']->lang,'','',true);

		/* ==================================================
			Authorisation
		================================================== */
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_odsfacebook_auth','uid='.intval($this->config['auth']));
		$auth=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$fb=new tx_odsfacebook_div($auth);

		/* ==================================================
			Template
		================================================== */
		$templateCode=$this->cObj->fileResource($conf['template']);
		$template['total']=$this->cObj->getSubpart($templateCode,'###ODS_FACEBOOK###');
		$template['item']=$this->cObj->getSubpart($template['total'],'###ITEM###');
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');

		/* ==================================================
			Facebook
		================================================== */
		$response=$fb->fetch_data($this->config['api_call']);
		if(is_array($response->data)){
			$i=1;
			$subpart['###ITEM###']='';
			foreach($response->data as $item){
				if(
					(empty($this->config['filter_user']) || $item->from->id==$this->config['filter_user']) && // Filter
					(!is_object($item->privacy) || $item->privacy->description=='Everyone' || strpos($item->privacy->description,$language)!==false) // Language
				){
					$data=array(
						'text'=>$this->createLinks($item->message),
						'date'=>date($conf['date_format'],strtotime($item->created_time)),
						'author'=>$item->from->name,
						'link'=>$item->link,
						'image'=>$item->picture,
 						'name'=>$item->name,
						'caption'=>$item->caption,
						'description'=>$item->description,
					);
					$local_cObj->start($data,'');
					foreach($data as $field=>$value){
						$marker['###'.strtoupper($field).'###']=$local_cObj->cObjGetSingle($conf['marker.'][$field],$conf['marker.'][$field.'.']);
					}
					$subpart['###ITEM###'].=$this->cObj->substituteMarkerArrayCached($template['item'],$marker);
					if($i++>=$this->config['num']) break; 
				}
			}
			$content=$this->cObj->substituteMarkerArrayCached($template['total'],array(),$subpart);
		}else{
			$content='';
		}

		return $this->pi_wrapInBaseClass($content);
	}

	function createLinks($text){
		$replace = array(
			'/(http|https):\/\/[^ ]*/i'=>'<a href="\\0">\\0</a>',
			'/#([^ ]*)/i'=>'<a href="http://www.facebook.com/\\1">\\0</a>',
		);
		return preg_replace(array_keys($replace),array_values($replace),$text);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_facebook/pi1/class.tx_odsfacebook_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ods_facebook/pi1/class.tx_odsfacebook_pi1.php']);
}

?>