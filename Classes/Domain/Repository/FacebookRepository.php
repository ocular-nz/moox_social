<?php
namespace TYPO3\MooxSocial\Domain\Repository;

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

/**
 *
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FacebookRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	protected $defaultOrderings = array ('updated' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING);
	
	protected $defaultStoragePid = 44;
	
	/**
	 * Finds all posts (overwrite)
	 *	
	 * @param string $pages The Facebook page ids to get posts from
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAll($pages = '') {
		$query = $this->createQuery();
				
		if($pages != ""){			
			$pages = explode(",",$pages);			
			$query->matching(
				$query->in('page', $pages)
			);
		}
		
		return $query						
			->execute();
	}
	
	/**
	 * Finds all posts by given request settings
	 *
	 * @param array $settings settings The settings
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAllBySettings($settings) {
		
		$query = $this->createQuery();
		
		/*
		print_r($query->getQuerySettings()->getStoragePageIds());
		
		if(count($query->getQuerySettings()->getStoragePageIds())<1){
			 $query->getQuerySettings()->setStoragePageIds(array($this->defaultStoragePid));
		} elseif(count($query->getQuerySettings()->getStoragePageIds())==1){
			 
			 $query->getQuerySettings()->setStoragePageIds(array($this->defaultStoragePid));
		}
		
		print_r($query->getQuerySettings()->getStoragePageIds());
		
		exit();
		
		*/
		
		if($settings['sort_direction'] == "DESC"){			
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
		} else {
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));			
		}
		
		if($settings['page_id'] != ""){
			$pages = explode(",",$settings['page_id']);			
			$query->matching(
				$query->in('page', $pages)
			);
		}
		
		return $query			
			->setOffset(intval($settings['offset']))
			->setLimit(intval($settings['limit']))
			->execute();
	}
	
	/**
	 * Finds all posts by given request settings directly from Facebook api
	 *
	 * @param array $settings settings The settings
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function requestAllBySettings($settings) {
		
		$result = array();		
						
		$postIds 	= array();
		
		if(!$settings['offset']){
			$settings['offset'] = 0;
		}
		
		$rawFeed 	= \TYPO3\MooxSocial\Controller\FacebookController::facebook($settings['api_app_id'],$settings['api_secret'],$settings['api_page_id'],"posts?offset=".($settings['offset'])."&limit=".($settings['limit']+$settings['offset']));
		
		$posts 		= array();
			
		foreach($rawFeed['data'] as $item) {
					
			if(!in_array($item['id'],$postIds) && $item['status_type']!=""){
						
				$postIds[] 		= $item['id'];					
				$postId 		= explode("_",$item['id']);
				$postId 		= $postId[1];
						
				$item['postId'] = $postId;
				$item['pageId'] = $pageId;
				$item['pid'] 	= $storagePid;
					
				$post 			= \TYPO3\MooxSocial\Controller\FacebookController::facebookPost($item);					
						
				if(is_array($post)){
					$posts[] 	= $post;
				}
			}	
		}			
				
		if(count($posts)){
			
			foreach($posts AS $post){				
					
				$facebookPost = new \TYPO3\MooxSocial\Domain\Model\Facebook;
							
				$facebookPost->setPid($post['pid']);
				$facebookPost->setCreated($post['created']);					
				$facebookPost->setUpdated($post['updated']);
				$facebookPost->setType($post['type']);
				$facebookPost->setStatusType($post['statusType']);
				$facebookPost->setPage($post['page']);
				$facebookPost->setAction($post['action']);
				$facebookPost->setTitle($post['title']);
				$facebookPost->setSummary($post['summary']);
				$facebookPost->setText($post['text']);
				$facebookPost->setAuthor($post['author']);
				$facebookPost->setAuthorId($post['authorId']);
				$facebookPost->setDescription($post['description']);
				$facebookPost->setCaption($post['caption']);
				$facebookPost->setUrl($post['url']);
				$facebookPost->setLinkName($post['linkName']);
				$facebookPost->setLinkUrl($post['linkUrl']);
				$facebookPost->setImageUrl($post['imageUrl']);
				$facebookPost->setImageEmbedcode($post['imageEmbedcode']);
				$facebookPost->setVideoUrl($post['videoUrl']);
				$facebookPost->setVideoEmbedcode($post['videoEmbedcode']);
				$facebookPost->setSharedUrl($post['sharedUrl']);
				$facebookPost->setSharedTitle($post['sharedTitle']);
				$facebookPost->setSharedDescription($post['sharedDescription']);
				$facebookPost->setSharedCaption($post['sharedCaption']);				
				$facebookPost->setLikes($post['likes']);
				$facebookPost->setShares($post['shares']);
				$facebookPost->setComments($post['comments']);
				$facebookPost->setApiUid($post['apiUid']);					
				$facebookPost->setApiHash($post['apiHash']);
					
				$result[] = $facebookPost;				
				
			}			
		}
		
		return $result;
	}
	
	/**
	 * Finds all posts by given page id
	 *
	 * @param string $pageId The page id
	 * @param integer $storagePid The storage pid
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAllByPageId($pageId,$storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query
			->matching(
				$query->equals('page', $pageId)
			)	
			->execute();	
	}
	
	/**
	 * Finds all posts by given storage pid
	 *	
	 * @param integer $storagePid The storage pid
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAllByStoragePid($storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query			
			->execute();	
	}
	
	/**
	 * Finds all posts
	 *
	 * @param string $pages The Facebook page ids to get posts from
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAllLimited($pages = '') {
		$query = $this->createQuery();		
		
		if($pages != ""){
			$query->in('page', explode(",",$pages));
		}
		
		return $query			
			->setLimit(50)
			->execute();
	}
	
	/**
	 * Finds one facebook post by the specified api uid
	 *
	 * @param string $apiUid api uid The api uid the post must refer to
	 * @param integer $storagePid The storage pid
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findOneByApiUid($apiUid,$storagePid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query
			->matching(
				$query->equals('apiUid', $apiUid)
			)
			->setLimit(1)
			->execute()
			->getFirst();
	}
	
	/**
	 * Finds one random facebook post
	 *	 
	 * @param string $pages The Facebook page ids to get posts from
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findRandomOne($pages = '') {
		$rows 		= $this->createQuery()->execute()->count();		
		$row_number = mt_rand(0, max(0, ($rows - 1)));		
		$query = $this->createQuery();
		
		if($pages != ""){
			$pages = explode(",",$pages);		
			$query->matching(
				$query->in('page', $pages)
			);
		}
		
		return $query
			->setOffset($row_number)
			->setLimit(1)
			->execute()
			->getFirst();
	}
	
	/**
	 * Removes posts by given page id
	 *
	 * @param string $pageId The Facebook page id
	 * @param integer $storagePid The storage pid
	 * @return void
	 */
	public function removeByPageId($pageId,$storagePid) {
		foreach ($this->findAllByPageId($pageId,$storagePid) AS $object) {          
			//$query = $this->createQuery();
			//$query->statement('DELETE FROM tx_mooxsocial_domain_model_facebook WHERE uid='.($object->getUid()).' LIMIT 1')->execute();						
			$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			   'tx_mooxsocial_domain_model_facebook',
			   'uid='.$object->getUid()
			);	
			//$this->remove($object);
        }		
	}		
	
	/**
	 * Removes posts by given storage pid
	 *
	 * @param integer $storagePid The storage pid
	 * @return void
	 */
	public function removeByStoragePid($storagePid) {
		foreach ($this->findAllByStoragePid($storagePid) AS $object) {          							
			$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			   'tx_mooxsocial_domain_model_facebook',
			   'uid='.$object->getUid()
			);				
        }		
	}	
}
?>