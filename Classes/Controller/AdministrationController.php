<?php
namespace DCNGmbH\MooxSocial\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Dominic Martin <dm@dcn.de>, DCN GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AdministrationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * facebookRepository
	 *
	 * @var \DCNGmbH\MooxSocial\Domain\Repository\FacebookRepository
	 * @inject
	 */
	protected $facebookRepository;
	
	/**
	 * twitterRepository
	 *
	 * @var \DCNGmbH\MooxSocial\Domain\Repository\TwitterRepository
	 * @inject
	 */
	protected $twitterRepository;

	/**
	 * youtubeRepository
	 *
	 * @var \DCNGmbH\MooxSocial\Domain\Repository\YoutubeRepository
	 * @inject
	 */
	protected $youtubeRepository;
	
	/**
	 * flickrRepository
	 *
	 * @var \DCNGmbH\MooxSocial\Domain\Repository\FlickrRepository
	 * @inject
	 */
	protected $flickrRepository;
	
	/**
	 * slideshareRepository
	 *
	 * @var \DCNGmbH\MooxSocial\Domain\Repository\SlideshareRepository
	 * @inject
	 */
	protected $slideshareRepository;
	
	/**
	 * action index
	 *
	 * @return void
	 */
	public function indexAction() {
		
		$folders = array();
		
		$addFolder = array();				
		$addFolder['uid'] 				= 0;
		$addFolder['title'] 			= LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.folder.listing.default_storage', $this->extensionName );
		$addFolder['countFacebook'] 	= $this->facebookRepository->findAllByStoragePid(0)->count();
		$addFolder['countTwitter'] 		= $this->twitterRepository->findAllByStoragePid(0)->count();
		$addFolder['countYoutube'] 		= $this->youtubeRepository->findAllByStoragePid(0)->count();
		$addFolder['countFlickr'] 		= $this->flickrRepository->findAllByStoragePid(0)->count();
		$addFolder['countSlideshare'] 	= $this->slideshareRepository->findAllByStoragePid(0)->count();
		$addFolder['count']   			= $addFolder['countFacebook']+$addFolder['countTwitter']+$addFolder['countYoutube']+$addFolder['countFlickr']+$addFolder['countSlideshare'];	
		
		$folders[] = $addFolder;
		
		$result = $this->getSocialFolders();
		
		foreach($result AS $folder){
						
			$addFolder = array();				
			$addFolder['uid'] 				= $folder['uid'];
			$addFolder['title'] 			= $folder['title'];
			$addFolder['countFacebook'] 	= $this->facebookRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countTwitter'] 		= $this->twitterRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countYoutube'] 		= $this->youtubeRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countFlickr'] 		= $this->flickrRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['countSlideshare'] 	= $this->slideshareRepository->findAllByStoragePid($folder['uid'])->count();
			$addFolder['count']   			= $addFolder['countFacebook']+$addFolder['countTwitter']+$addFolder['countYoutube']+$addFolder['countFlickr']+$addFolder['countSlideshare'];	
			$folders[] = $addFolder;			
		}
		
		$this->view->assign('folders', $folders);				
	}
	
	/**
	 * action truncate folder
	 *	
	 * @param integer $storagePid
	 * @return void
	 */
	public function truncateFolderAction($storagePid) {
		$this->facebookRepository->removeByStoragePid($storagePid);
		$this->addFlashMessage(
			LocalizationUtility::translate( 'LLL:EXT:moox_social/Resources/Private/Language/locallang.xlf:overview.folder.listing.truncate.success', $this->extensionName ),
			'',
			FlashMessage::OK
		);
		$this->redirect('index');
	}

	/**
	 * Get array of folders with social module	
	 *	
	 * @return	array	folders with social module	
	 */
	public function getSocialFolders() {
		
		$query = array(
			'SELECT' => '*',
			'FROM' => 'pages',
			'WHERE' => "deleted=0 AND doktype=254 AND module='mxsocial'"
		);
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		return $pages;		
	}	
	
	/**
	 * Get array of page ids with social plugins	
	 *	
	 * @param string $listType
	 * @return	array	page ids with social plugins	
	 */
	public function getSocialPluginPids($listType = "mooxsocial_") {
		
		$query = array(
			'SELECT' => 'DISTINCT pid',
			'FROM' => 'tt_content',
			'WHERE' => 'list_type LIKE "'.$listType.'%" AND deleted=0 AND hidden=0'
		);
		$pids = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
		return $pids;		
	}
	
	/**
	 * clear cache
	 *
	 * @param string $listType
	 * @param array $clearCachePages
	 * @return void
	 */
	public function clearCache($listType = "mooxsocial_", $clearCachePages = array()) {				
		
		$pages = self::getSocialPluginPids($listType);
		
		if(is_array($clearCachePages) && count($clearCachePages)){
			$pids = $clearCachePages;
		} else {
			$pids = array();
		}
		
		foreach ($pages as $page) {
			if(!in_array($page['pid'],$pids)){
				$pids[] = $page['pid'];
			}		
		}
		
		if(count($pids)){
			$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			$cacheService = $objectManager->get('TYPO3\\CMS\\Extbase\\Service\\CacheService');
			$cacheService->clearPageCache($pids);
		}		
	}
}
?>