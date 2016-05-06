<?php
namespace DCNGmbH\MooxSocial\Domain\Repository;

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
class SlideshareRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	protected $defaultOrderings = array ('updated' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING);
	
	protected $defaultStoragePid = 44;
	
	/**
	 * Finds all posts (overwrite)
	 *	
	 * @param string $pages The Slideshare page ids to get posts from
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
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
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
	 */
	public function findAllBySettings($settings) {
		
		$query = $this->createQuery();
		
		if($settings['sort_direction'] == "DESC"){			
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
		} else {
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));			
		}
		
		if($settings['user_id'] != ""){
			$pages = explode(",",$settings['user_id']);			
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
	 * Finds all posts by given request settings directly from Slideshare api
	 *
	 * @param array $settings settings The settings
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
	 */
	public function requestAllBySettings($settings) {
		
		$result = array();		
						
		$postIds 	= array();
		
		if(!$settings['offset']){
			$settings['offset'] = 0;
		}
		
		$rawFeed 	= \DCNGmbH\MooxSocial\Controller\SlideshareController::slideshare($settings['api_user_id']);

		$posts 		= array();
			
		foreach($rawFeed as $item) {
					
			if(!in_array($item['ID'],$postIds)){
						
				$postIds[] 		= $item['ID'];					
				$postId 		= $item['ID'];	
						
				$item['id'] 	        = $postId;
				$item['userId'] 	= $userId;
				$item['pid'] 		= $storagePid;
					
				$post 			= \DCNGmbH\MooxSocial\Controller\SlideshareController::slidesharePost($item);
						
				if(is_array($post)){
					$posts[] 	= $post;
				}
			}	
		}			
				
		if(count($posts)){
			
			foreach($posts AS $post){				
					
				$slidesharePost = new \DCNGmbH\MooxSocial\Domain\Model\Slideshare;
							
				$slidesharePost->setPid($post['pid']);
				$slidesharePost->setCreated($post['created']);					
				$slidesharePost->setUpdated($post['updated']);
				$slidesharePost->setType($post['type']);
				$slidesharePost->setStatusType($post['statusType']);
				$slidesharePost->setPage($post['page']);
				$slidesharePost->setAction($post['action']);
				$slidesharePost->setTitle($post['title']);
				$slidesharePost->setSummary($post['summary']);
				$slidesharePost->setText($post['text']);
				$slidesharePost->setAuthor($post['author']);
				$slidesharePost->setAuthorId($post['authorId']);
				$slidesharePost->setDescription($post['description']);
				$slidesharePost->setCaption($post['caption']);
				$slidesharePost->setUrl($post['url']);
				$slidesharePost->setLinkName($post['linkName']);
				$slidesharePost->setLinkUrl($post['linkUrl']);
				$slidesharePost->setImageUrl($post['imageUrl']);
				$slidesharePost->setImageEmbedcode($post['imageEmbedcode']);
				$slidesharePost->setVideoUrl($post['videoUrl']);
				$slidesharePost->setVideoEmbedcode($post['videoEmbedcode']);
				$slidesharePost->setSharedUrl($post['sharedUrl']);
				$slidesharePost->setSharedTitle($post['sharedTitle']);
				$slidesharePost->setSharedDescription($post['sharedDescription']);
				$slidesharePost->setSharedCaption($post['sharedCaption']);				
				$slidesharePost->setLikes($post['likes']);
				$slidesharePost->setShares($post['shares']);
				$slidesharePost->setComments($post['comments']);
				$slidesharePost->setApiUid($post['apiUid']);					
				$slidesharePost->setApiHash($post['apiHash']);
					
				$result[] = $slidesharePost;				
				
			}			
		}
		
		return $result;
	}
	
	/**
	 * Finds all posts by given slideshare channel
	 *
	 * @param string $userId The user id
	 * @param integer $storagePid The storage pid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
	 */
	public function findAllByPageId($userId,$storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query
			->matching(
				$query->equals('page', $userId)
			)	
			->execute();	
	}
	
	/**
	 * Finds all posts by given storage pid
	 *	
	 * @param integer $storagePid The storage pid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
	 */
	public function findAllByStoragePid($storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query->execute();
	}
	
	/**
	 * Finds all posts
	 *
	 * @param string $pages The slideshare page ids to get posts from
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
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
	 * Finds one slideshare post by the specified api uid
	 *
	 * @param string $apiUid api uid The api uid the post must refer to
	 * @param integer $storagePid The storage pid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
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
	 * Finds one random slideshare slideshow
	 *	 
	 * @param string $pages The Slideshare page ids to get posts from
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
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
	 * Removes posts by given youtbeChannel
	 *
	 * @param string $userId The user id
	 * @param integer $storagePid The storage pid
	 * @return void
	 */
	public function removeByPageId($userId,$storagePid) {
		foreach ($this->findAllByPageId($userId,$storagePid) AS $object) {          							
			$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			   'tx_mooxsocial_domain_model_slideshare',
			   'uid='.$object->getUid()
			);				
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
			   'tx_mooxsocial_domain_model_slideshare',
			   'uid='.$object->getUid()
			);				
        }		
	}	
}
?>