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
class TwitterRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	protected $defaultOrderings = array ('updated' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING);
	
	/**
	 * Finds all posts (overwrite)
	 *	
	 * @param string $pages The Twitter page ids to get posts from
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
		
		if($settings['sort_direction'] == "DESC"){			
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
		} else {
			$query->setOrderings (Array($settings['sort_by'] => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));			
		}
		
		if($settings['screen_name'] != ""){
			$pages = explode(",",$settings['screen_name']);			
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
	 * Finds all posts by given request settings directly from Twitter api
	 *
	 * @param array $settings settings The settings
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function requestAllBySettings($settings) {
		
		$result 	= array();							
		$postIds 	= array();
		
		if(!$settings['offset']){
			$settings['offset'] = 0;
		}
		
		$rawFeed 	= \TYPO3\MooxSocial\Controller\TwitterController::twitter($settings['api_oauth_access_token'],$settings['api_oauth_access_token_secret'],$settings['api_consumer_key'],$settings['api_consumer_key_secret'],$settings['api_screen_name']);

		$posts 		= array();
			
		foreach($rawFeed as $item) {
					
			if(!in_array($item['id'],$postIds)){
						
				$postIds[] 		= $item['id'];					
				$postId 		= $item['id'];	
						
				$item['postId'] 	= $postId;
				$item['pageId'] 	= $screenName;
				$item['pid'] 		= $storagePid;
					
				$post 			= \TYPO3\MooxSocial\Controller\TwitterController::twitterPost($item);					
						
				if(is_array($post)){
					$posts[] 	= $post;
				}
			}	
		}			
				
		if(count($posts)){
			
			foreach($posts AS $post){				
					
				$twitterPost = new \TYPO3\MooxSocial\Domain\Model\Twitter;
							
				$twitterPost->setPid($post['pid']);
				$twitterPost->setCreated($post['created']);					
				$twitterPost->setUpdated($post['updated']);
				$twitterPost->setType($post['type']);
				$twitterPost->setStatusType($post['statusType']);
				$twitterPost->setPage($post['page']);
				$twitterPost->setAction($post['action']);
				$twitterPost->setTitle($post['title']);
				$twitterPost->setSummary($post['summary']);
				$twitterPost->setText($post['text']);
				$twitterPost->setAuthor($post['author']);
				$twitterPost->setAuthorId($post['authorId']);
				$twitterPost->setDescription($post['description']);
				$twitterPost->setCaption($post['caption']);
				$twitterPost->setUrl($post['url']);
				$twitterPost->setLinkName($post['linkName']);
				$twitterPost->setLinkUrl($post['linkUrl']);
				$twitterPost->setImageUrl($post['imageUrl']);
				$twitterPost->setImageEmbedcode($post['imageEmbedcode']);
				$twitterPost->setVideoUrl($post['videoUrl']);
				$twitterPost->setVideoEmbedcode($post['videoEmbedcode']);
				$twitterPost->setSharedUrl($post['sharedUrl']);
				$twitterPost->setSharedTitle($post['sharedTitle']);
				$twitterPost->setSharedDescription($post['sharedDescription']);
				$twitterPost->setSharedCaption($post['sharedCaption']);				
				$twitterPost->setLikes($post['likes']);
				$twitterPost->setShares($post['shares']);
				$twitterPost->setComments($post['comments']);
				$twitterPost->setApiUid($post['apiUid']);					
				$twitterPost->setApiHash($post['apiHash']);
					
				$result[] = $twitterPost;				
				
			}			
		}
		
		return $result;
	}
	
	/**
	 * Finds all posts by given screen name
	 *
	 * @param string $screenName The screen name
	 * @param integer $storagePid The storage pid
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findAllByPageId($screenName,$storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query
			->matching(
				$query->equals('page', $screenName)
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
	 * @param string $pages The Twitter page ids to get posts from
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
	 * Finds one twitter post by the specified api uid
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
	 * Finds one random twitter post
	 *	 
	 * @param string $pages The Twitter page ids to get posts from
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
	 * Removes posts by given screenName
	 *
	 * @param string $screenName The Twitter screen name
	 * @param integer $storagePid The storage pid
	 * @return void
	 */
	public function removeByPageId($screenName,$storagePid) {
		foreach ($this->findAllByPageId($screenName,$storagePid) AS $object) {          							
			$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			   'tx_mooxsocial_domain_model_twitter',
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
			   'tx_mooxsocial_domain_model_twitter',
			   'uid='.$object->getUid()
			);				
        }		
	}	
}
?>