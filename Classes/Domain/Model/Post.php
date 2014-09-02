<?php
namespace TYPO3\MooxSocial\Domain\Model;

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
class Post extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	
	/**
	 * Erstellt am
	 *
	 * @var DateTime
	 */
	protected $created;
	
	/**
	 * Geändert am
	 *
	 * @var DateTime
	 */
	protected $updated;
	
	/**
	 * Model
	 *
	 * @var \string
	 */
	protected $model;
	
	/**
	 * Typ
	 *
	 * @var \string
	 */
	protected $type;
	
	/**
	 * Status-Typ
	 *
	 * @var \string
	 */
	protected $statusType;
	
	/**
	 * Page
	 *
	 * @var \string
	 */
	protected $page;
	
	/**
	 * Action
	 *
	 * @var \string
	 */
	protected $action;
	
	/**
	 * Titel
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $title;
	
	/**
	 * Zusammenfassung
	 *
	 * @var \string
	 */
	protected $summary;
	
	/**
	 * Text
	 *
	 * @var \string
	 */
	protected $text;

	/**
	 * Autor
	 *
	 * @var \string
	 */
	protected $author;
	
	/**
	 * Autor-ID
	 *
	 * @var \string
	 */
	protected $authorId;
	
	/**
	 * Beschreibung
	 *
	 * @var \string
	 */
	protected $description;
	
	/**
	 * Beschriftung
	 *
	 * @var \string
	 */
	protected $caption;
	
	/**
	 * Url
	 *
	 * @var \string
	 */
	protected $url;
	
	/**
	 * Link-Name
	 *
	 * @var \string
	 */
	protected $linkName;
	
	/**
	 * Link-Url
	 *
	 * @var \string
	 */
	protected $linkUrl;
	
	/**
	 * Bild-Url
	 *
	 * @var \string
	 */
	protected $imageUrl;
	
	/**
	 * Bild-Embed-Code
	 *
	 * @var \string
	 */
	protected $imageEmbedcode;
	
	/**
	 * Video-Url
	 *
	 * @var \string
	 */
	protected $videoUrl;
	
	/**
	 * Video-Embed-Code
	 *
	 * @var \string
	 */
	protected $videoEmbedcode;

	/**
	 * geteilte Url
	 *
	 * @var \string
	 */
	protected $sharedUrl;
	
	/**
	 *  geteilter Titel
	 *
	 * @var \string
	 */
	protected $sharedTitle;
	
	/**
	 *  geteilte Beschreibung
	 *
	 * @var \string
	 */
	protected $sharedDescription;
	
	/**
	 *  geteilte Beschriftung
	 *
	 * @var \string
	 */
	protected $sharedCaption;

	/**
	 *  Anzahl Likes
	 *
	 * @var integer
	 */
	protected $likes;
	
	/**
	 *  Anzahl Shares
	 *
	 * @var integer
	 */
	protected $shares;
	
	/**
	 *  Anzahl Kommentare
	 *
	 * @var integer
	 */
	protected $comments;
	
	/**
	 *  API uid
	 *
	 * @var \string
	 */
	protected $apiUid;
	
	/**
	 *  API hash
	 *
	 * @var \string
	 */
	protected $apiHash;
	
	/**
	 * Get created
	 *
	 * @return DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * Set created
	 *
	 * @param integer $created created
	 * @return void
	 */
	public function setCreated($created) {
		$this->created = $created;
	}
	
	/**
	 * Get updated
	 *
	 * @return DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * Set updated
	 *
	 * @param integer $updated updated
	 * @return void
	 */
	public function setUpdated($updated) {
		$this->updated = $updated;
	}
	
	/**
	 * Returns the model
	 *
	 * @return \string $model
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Sets the model
	 *
	 * @param \string $model
	 * @return void
	 */
	public function setModel($model) {
		$this->model = $model;
	}
	
	/**
	 * Returns the type
	 *
	 * @return \string $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Sets the type
	 *
	 * @param \string $type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}
	
	/**
	 * Returns the status type
	 *
	 * @return \string $statusType
	 */
	public function getStatusType() {
		return $this->statusType;
	}

	/**
	 * Sets the statusType
	 *
	 * @param \string $statusType
	 * @return void
	 */
	public function setStatusType($statusType) {
		$this->statusType = $statusType;
	}
	
	/**
	 * Returns the page
	 *
	 * @return \string $page
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the page
	 *
	 * @param \string $page
	 * @return void
	 */
	public function setPage($page) {
		$this->page = $page;
	}
	
	/**
	 * Returns the action
	 *
	 * @return \string $action
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Sets the action
	 *
	 * @param \string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * Returns the title
	 *
	 * @return \string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param \string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Returns the summary
	 *
	 * @return \string $summary
	 */
	public function getSummary() {
		return $this->summary;
	}

	/**
	 * Sets the summary
	 *
	 * @param \string $summary
	 * @return void
	 */
	public function setSummary($summary) {
		$this->summary = $summary;
	}
	
	/**
	 * Returns the text
	 *
	 * @return \string $text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Sets the text
	 *
	 * @param \string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
	 * Returns the author
	 *
	 * @return \string $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 *
	 * @param \string $author
	 * @return void
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}
	
	/**
	 * Returns the author id
	 *
	 * @return \string $authorId
	 */
	public function getAuthorId() {
		return $this->authorId;
	}

	/**
	 * Sets the author id
	 *
	 * @param \string $authorId
	 * @return void
	 */
	public function setAuthorId($authorId) {
		$this->authorId = $authorId;
	}
	
	/**
	 * Returns the description
	 *
	 * @return \string $description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Sets the description
	 *
	 * @param \string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Returns the caption
	 *
	 * @return \string $caption
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 * Sets the caption
	 *
	 * @param \string $caption
	 * @return void
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}

	/**
	 * Returns the url
	 *
	 * @return \string $url
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * Sets the url
	 *
	 * @param \string $url
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * Sets the link name
	 *
	 * @param \string $linkName
	 * @return void
	 */
	public function setLinkName($linkName) {
		$this->linkName = $linkName;
	}
	
	/**
	 * Returns the link name
	 *
	 * @return \string $linkName
	 */
	public function getLinkName() {
		return $this->linkName;
	}
	
	/**
	 * Sets the link url
	 *
	 * @param \string $linkUrl
	 * @return void
	 */
	public function setLinkUrl($linkUrl) {
		$this->linkUrl = $linkUrl;
	}
	
	/**
	 * Returns the link url
	 *
	 * @return \string $linkUrl
	 */
	public function getLinkUrl() {
		return $this->linkUrl;
	}

	/**
	 * Returns the image url
	 *
	 * @return \string $imageUrl
	 */
	public function getImageUrl() {
		return $this->imageUrl;
	}

	/**
	 * Sets the image url
	 *
	 * @param \string $imageUrl
	 * @return void
	 */
	public function setImageUrl($imageUrl) {
		$this->imageUrl = $imageUrl;
	}
	
	/**
	 * Returns the image embedcode
	 *
	 * @return \string $imageEmbedcode
	 */
	public function getImageEmbedcode() {
		return $this->imageEmbedcode;
	}

	/**
	 * Sets the image embedcode
	 *
	 * @param \string $imageEmbedcode
	 * @return void
	 */
	public function setImageEmbedcode($imageEmbedcode) {
		$this->imageEmbedcode = $imageEmbedcode;
	}
	
	/**
	 * Returns the video url
	 *
	 * @return \string $videoUrl
	 */
	public function getVideoUrl() {
		return $this->videoUrl;
	}

	/**
	 * Sets the video url
	 *
	 * @param \string $videoUrl
	 * @return void
	 */
	public function setVideoUrl($videoUrl) {
		$this->videoUrl = $videoUrl;
	}
	
	/**
	 * Returns the video embedcode
	 *
	 * @return \string $videoEmbedcode
	 */
	public function getVideoEmbedcode() {
		return $this->videoEmbedcode;
	}

	/**
	 * Sets the video embedcode
	 *
	 * @param \string $videoEmbedcode
	 * @return void
	 */
	public function setVideoEmbedcode($videoEmbedcode) {
		$this->videoEmbedcode = $videoEmbedcode;
	}

	/**
	 * Returns the shared url
	 *
	 * @return \string $sharedUrl
	 */
	public function getSharedUrl() {
		return $this->sharedUrl;
	}

	/**
	 * Sets the shared url
	 *
	 * @param \string $sharedUrl
	 * @return void
	 */
	public function setSharedUrl($sharedUrl) {
		$this->sharedUrl = $sharedUrl;
	}
	
	/**
	 * Returns the shared title
	 *
	 * @return \string $sharedTitle
	 */
	public function getSharedTitle() {
		return $this->sharedTitle;
	}

	/**
	 * Sets the shared title
	 *
	 * @param \string $sharedTitle
	 * @return void
	 */
	public function setSharedTitle($sharedTitle) {
		$this->sharedTitle = $sharedTitle;
	}
	
	/**
	 * Returns the shared description
	 *
	 * @return \string $sharedDescription
	 */
	public function getSharedDescription() {
		return $this->sharedDescription;
	}

	/**
	 * Sets the shared description
	 *
	 * @param \string $sharedDescription
	 * @return void
	 */
	public function setSharedDescription($sharedDescription) {
		$this->sharedDescription = $sharedDescription;
	}
	
	/**
	 * Returns the shared caption
	 *
	 * @return \string $sharedCaption
	 */
	public function getSharedCaption() {
		return $this->sharedCaption;
	}

	/**
	 * Sets the shared caption
	 *
	 * @param \string $sharedCaption
	 * @return void
	 */
	public function setSharedCaption($sharedCaption) {
		$this->sharedCaption = $sharedCaption;
	}
	
	/**
	 * Returns the likes
	 *
	 * @return integer
	 */
	public function getLikes() {
		return $this->likes;
	}

	/**
	 * Set the likes
	 *
	 * @param integer $likes likes
	 * @return void
	 */
	public function setLikes($likes) {
		$this->likes = $likes;
	}
	
	/**
	 * Returns the shares
	 *
	 * @return integer
	 */
	public function getShares() {
		return $this->shares;
	}

	/**
	 * Set the shares
	 *
	 * @param integer $shares shares
	 * @return void
	 */
	public function setShares($shares) {
		$this->shares = $shares;
	}
	
	/**
	 * Returns the comments
	 *
	 * @return integer
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * Set the comments
	 *
	 * @param integer $comments comments
	 * @return void
	 */
	public function setComments($comments) {
		$this->comments = $comments;
	}
	
	/**
	 * Returns the api uid
	 *
	 * @return \string $apiUid
	 */
	public function getApiUid() {
		return $this->apiUid;
	}

	/**
	 * Set the api uid
	 *
	 * @param \string $apiUid api uid
	 * @return void
	 */
	public function setApiUid($apiUid) {
		$this->apiUid = $apiUid;
	}
	
	/**
	 * Returns the api hash
	 *
	 * @return \string $apiHash
	 */
	public function getApiHash() {
		return $this->apiHash;
	}

	/**
	 * Sets the api hash
	 *
	 * @param \string $apiHash
	 * @return void
	 */
	public function setApiHash($apiHash) {
		$this->apiHash = $apiHash;
	}
}
?>