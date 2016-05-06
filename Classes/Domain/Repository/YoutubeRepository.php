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
class YoutubeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	protected $defaultOrderings = array ('updated' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING);
	
	protected $defaultStoragePid = 44;
	
	/**
	 * Finds all posts (overwrite)
	 *	
	 * @param string $pages The Youtube page ids to get posts from
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
		
		if($settings['youtube_channel'] != ""){
			$pages = explode(",",$settings['youtube_channel']);			
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
	 * Finds all posts by given request settings directly from Youtube api
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
		
		$rawFeed 	= \DCNGmbH\MooxSocial\Controller\YoutubeController::youtube($settings['api_youtube_channel']);

		$posts 		= array();
			
		foreach($rawFeed as $item) {
					
			if(!in_array($item['id'],$postIds)){
						
				$postIds[] 		= $item['id'];					
				$postId 		= $item['id'];	
						
				$item['id'] 	= $postId;
				$item['youtubeChannel'] 	= $youtubeChannel;
				$item['pid'] 		= $storagePid;
					
				$post 			= \DCNGmbH\MooxSocial\Controller\YoutubeController::youtubePost($item);
						
				if(is_array($post)){
					$posts[] 	= $post;
				}
			}	
		}			
				
		if(count($posts)){
			
			foreach($posts AS $post){				
					
				$youtubePost = new \DCNGmbH\MooxSocial\Domain\Model\Youtube;
							
				$youtubePost->setPid($post['pid']);
				$youtubePost->setCreated($post['created']);					
				$youtubePost->setUpdated($post['updated']);
				$youtubePost->setType($post['type']);
				$youtubePost->setStatusType($post['statusType']);
				$youtubePost->setPage($post['page']);
				$youtubePost->setAction($post['action']);
				$youtubePost->setTitle($post['title']);
				$youtubePost->setSummary($post['summary']);
				$youtubePost->setText($post['text']);
				$youtubePost->setAuthor($post['author']);
				$youtubePost->setAuthorId($post['authorId']);
				$youtubePost->setDescription($post['description']);
				$youtubePost->setCaption($post['caption']);
				$youtubePost->setUrl($post['url']);
				$youtubePost->setLinkName($post['linkName']);
				$youtubePost->setLinkUrl($post['linkUrl']);
				$youtubePost->setImageUrl($post['imageUrl']);
				$youtubePost->setImageEmbedcode($post['imageEmbedcode']);
				$youtubePost->setVideoUrl($post['videoUrl']);
				$youtubePost->setVideoEmbedcode($post['videoEmbedcode']);
				$youtubePost->setSharedUrl($post['sharedUrl']);
				$youtubePost->setSharedTitle($post['sharedTitle']);
				$youtubePost->setSharedDescription($post['sharedDescription']);
				$youtubePost->setSharedCaption($post['sharedCaption']);				
				$youtubePost->setLikes($post['likes']);
				$youtubePost->setShares($post['shares']);
				$youtubePost->setComments($post['comments']);
				$youtubePost->setApiUid($post['apiUid']);					
				$youtubePost->setApiHash($post['apiHash']);
					
				$result[] = $youtubePost;				
				
			}			
		}
		
		return $result;
	}
	
	/**
	 * Finds all posts by given youtube channel
	 *
	 * @param string $youtubeChannel The youtube channel
	 * @param integer $storagePid The storage pid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface The posts
	 */
	public function findAllByPageId($youtubeChannel,$storagePid) {		
		$query = $this->createQuery();		
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->getQuerySettings()->setIncludeDeleted(TRUE);
		return $query
			->matching(
				$query->equals('page', $youtubeChannel)
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
		return $query			
			->execute();	
	}
	
	/**
	 * Finds all posts
	 *
	 * @param string $pages The Youtube page ids to get posts from
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
	 * Finds one youtube post by the specified api uid
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
	 * Finds one random Youtube video
	 *	 
	 * @param string $pages The Youtube page ids to get posts from
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
	 * @param string $youtubeChannel The Youtube channel
	 * @param integer $storagePid The storage pid
	 * @return void
	 */
	public function removeByPageId($youtubeChannel,$storagePid) {
		foreach ($this->findAllByPageId($youtubeChannel,$storagePid) AS $object) {          							
			$result = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
			   'tx_mooxsocial_domain_model_youtube',
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
			   'tx_mooxsocial_domain_model_youtube',
			   'uid='.$object->getUid()
			);				
        }		
	}	
}
?>