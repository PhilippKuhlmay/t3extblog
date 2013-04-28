<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Domain_Model_Comment extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * author
	 *
	 * @var string
	 */
	protected $author;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * website
	 *
	 * @var string
	 */
	protected $website;

	/**
	 * date
	 *
	 * @var DateTime
	 */
	protected $date;

	/**
	 * text
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $text;

	/**
	 * approved
	 *
	 * @var boolean
	 */
	protected $approved = FALSE;

	/**
	 * spam
	 *
	 * @var boolean
	 */
	protected $spam = FALSE;

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the author
	 *
	 * @return string $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 *
	 * @param string $author
	 * @return void
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the website
	 *
	 * @return string $website
	 */
	public function getWebsite() {
		return $this->website;
	}

	/**
	 * Sets the website
	 *
	 * @param string $website
	 * @return void
	 */
	public function setWebsite($website) {
		$this->website = $website;
	}

	/**
	 * Returns the date
	 *
	 * @return DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param DateTime $date
	 * @return void
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * Returns the text
	 *
	 * @return string $text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Sets the text
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * Returns the approved
	 *
	 * @return boolean $approved
	 */
	public function getApproved() {
		return $this->approved;
	}

	/**
	 * Sets the approved
	 *
	 * @param boolean $approved
	 * @return void
	 */
	public function setApproved($approved) {
		$this->approved = $approved;
	}

	/**
	 * Returns the boolean state of approved
	 *
	 * @return boolean
	 */
	public function isApproved() {
		return $this->getApproved();
	}

	/**
	 * Returns the spam
	 *
	 * @return boolean $spam
	 */
	public function getSpam() {
		return $this->spam;
	}

	/**
	 * Sets the spam
	 *
	 * @param boolean $spam
	 * @return void
	 */
	public function setSpam($spam) {
		$this->spam = $spam;
	}

	/**
	 * Returns the boolean state of spam
	 *
	 * @return boolean
	 */
	public function isSpam() {
		return $this->getSpam();
	}

}
?>