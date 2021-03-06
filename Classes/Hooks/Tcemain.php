<?php

namespace FelixNagel\T3extblog\Hooks;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Service\CommentNotificationService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Container;
use FelixNagel\T3extblog\Service\FlushCacheService;

/**
 * Class being included by TCEmain using a hook.
 */
class Tcemain
{
    /**
     * Fields to check for changes.
     *
     * @var array
     */
    protected $watchedFields = [
        'approved',
        'spam',
    ];

    /**
     * notificationService.
     *
     * @var \FelixNagel\T3extblog\Service\CommentNotificationService
     */
    protected $notificationService = null;

    /**
     * objectContainer.
     *
     * @var Container
     */
    protected $objectContainer = null;

    /**
     * commentRepository.
     *
     * @var CommentRepository
     */
    protected $commentRepository = null;

    /**
     * Before processing: clear cache.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     *
     * @param string      $command            The TCEmain operation status, fx. 'update'
     * @param string      $table              The table TCEmain is currently processing
     * @param string      $id                 The records id (if any)
     * @param array       $relativeTo         Filled if command is relative to another element
     * @param DataHandler $tceMain            Reference to the parent object (TCEmain)
     * @param bool        $commandIsProcessed If the command has been processed
     */
    public function processCmdmap($command, $table, $id, $relativeTo, $commandIsProcessed, $tceMain)
    {
        if (!in_array($table, ['tx_t3blog_post', 'tx_t3blog_com'])) {
            return;
        }

        if ($command === 'delete') {
            $pid = $this->resolveRecordPid($table, $id, $tceMain);
            $tagsToFlush = [];

            // Cache tags
            if ($table === 'tx_t3blog_post') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
                $tagsToFlush[] = $table.'_uid_'.$id;
            }
            if ($table === 'tx_t3blog_com') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }

            FlushCacheService::flushFrontendCacheByTags($tagsToFlush);
        }
    }

    /**
     * After processing: delete related objects.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     *
     * @param string      $command    The TCEmain operation status, fx. 'update'
     * @param string      $table      The table TCEmain is currently processing
     * @param string      $id         The records id (if any)
     * @param array       $relativeTo Filled if command is relative to another element
     * @param DataHandler $tceMain    Reference to the parent object (TCEmain)
     */
    public function processCmdmap_postProcess($command, $table, $id, $relativeTo, $tceMain)
    {
        if ($command === 'delete') {
            if ($table === 'tx_t3blog_post') {
                $this->deletePostRelations(intval($id));
            }
        }
    }

    /**
     * TCEmain hook function for on-the-fly email sending
     * Hook: processDatamap_afterDatabaseOperations.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     *
     * @param string      $status  The command which has been sent to processDatamap
     * @param string      $table   The table we're dealing with
     * @param mixed       $id      Either the record UID or a string if a new record has been created
     * @param array       $fields  The record row how it has been inserted into the database
     * @param DataHandler $tceMain A reference to the TCEmain instance
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fields, $tceMain)
    {
        if (!in_array($table, ['tx_t3blog_post', 'tx_t3blog_com', 'tx_t3blog_cat'])) {
            return;
        }

        $pid = $this->resolveRecordPid($table, $id, $tceMain, $fields);
        $id = $this->resolveRecordUid($id, $tceMain);

        $tagsToFlush = [];

        if ($table === 'tx_t3blog_post') {
            // Cache tags for posts
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
                $tagsToFlush[] = $table.'_uid_'.$id;
            }
        }

        if ($table === 'tx_t3blog_com') {
            if ($status == 'update') {
                if ($this->isUpdateNeeded($fields, $this->watchedFields)) {
                    $this->processChangedComment($id);
                }
            }

            if ($status === 'new') {
                $this->processNewComment($id);
            }

            // Cache tags for comments
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }
        }

        if ($table === 'tx_t3blog_cat') {
            // Cache tags for categories
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }
        }

        FlushCacheService::flushFrontendCacheByTags($tagsToFlush);
    }

    /**
     * Deletes all data associated with the post when post is deleted.
     *
     * @param int $id
     */
    protected function deletePostRelations($id)
    {
        $command = [
            'tx_t3blog_com' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com', 'fk_post'),
            'tx_t3blog_com_nl' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com_nl', 'post_uid'),
            'tx_t3blog_trackback' => $this->getDeleteArrayForTable($id, 'tx_t3blog_trackback', 'postid'),
            'tt_content' => $this->getDeleteArrayForTable($id, 'tt_content', 'irre_parentid'),
        ];

        /* @var $tceMain DataHandler */
        $tceMain = $this->getObjectContainer()->getInstance(DataHandler::class);

        $tceMain->start([], $command);
        $tceMain->process_cmdmap();
    }

    /**
     * @param int $postId
     * @param string $tableName
     * @param string $fieldName
     *
     * @return array
     */
    protected function getDeleteArrayForTable($postId, $tableName, $fieldName)
    {
        $command = [];

        $connectionPool =  GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $constraints = [
            $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter($postId, \PDO::PARAM_INT))
        ];

        if ($tableName = 'tt_content') {
            $constraints[] = $queryBuilder->expr()->eq(
                'irre_parenttable',
                $queryBuilder->createNamedParameter('tx_t3blog_post')
            );
        }

        $queryBuilder
            ->select('uid')
            ->from($tableName)
            ->where(new CompositeExpression(CompositeExpression::TYPE_AND, $constraints));

        $rows = $queryBuilder->execute()->fetchAll();

        foreach ($rows as $record) {
            $command[$record['uid']]['delete'] = 1;
        }

        return $command;
    }

    /**
     * @param int $id
     *
     * @internal param int $fields
     */
    protected function processNewComment($id)
    {
        $this->getNotificationService()->processNewEntity($this->getComment($id));
    }

    /**
     * @param int $id
     *
     * @internal param int $fields
     */
    protected function processChangedComment($id)
    {
        $this->getNotificationService()->processChangedStatus($this->getComment($id));
    }

    /**
     * Get comment.
     *
     * @param int $uid Page uid
     *
     * @return Comment
     */
    protected function getComment($uid)
    {
        /* @var $comment Comment */
        $comment = $this->getCommentRepository()->findByUid($uid);

        return $comment;
    }

    /**
     * Get comment repository.
     *
     * @return CommentRepository
     */
    protected function getCommentRepository()
    {
        if ($this->commentRepository === null) {
            $this->commentRepository = $this->getObjectContainer()->getInstance(CommentRepository::class);
        }

        return $this->commentRepository;
    }

    /**
     * Get object container.
     *
     * @return Container
     */
    protected function getObjectContainer()
    {
        if ($this->objectContainer === null) {
            $this->objectContainer = GeneralUtility::makeInstance(Container::class);
        }

        return $this->objectContainer;
    }

    /**
     * Get notification service.
     *
     * @return CommentNotificationService
     */
    protected function getNotificationService()
    {
        if ($this->notificationService === null) {
            $this->notificationService = $this->getObjectContainer()->getInstance(CommentNotificationService::class);
        }

        return $this->notificationService;
    }

    /**
     * Get record uid.
     *
     * @param int         $id
     * @param DataHandler $reference
     *
     * @return int
     */
    protected function resolveRecordUid($id, DataHandler $reference)
    {
        if (false !== strpos($id, 'NEW')) {
            if (false === empty($reference->substNEWwithIDs[$id])) {
                $id = $reference->substNEWwithIDs[$id];
            }
        }

        return (int) $id;
    }
    /**
     * Get record pid.
     *
     * @param string      $table
     * @param int         $id
     * @param DataHandler $tceMain
     * @param array       $fields
     *
     * @return int
     */
    protected function resolveRecordPid($table, $id, $tceMain, $fields = null)
    {
        // Changed records
        if (isset($tceMain->checkValue_currentRecord['pid'])) {
            return (int) $tceMain->checkValue_currentRecord['pid'];
        }

        // New records
        if (is_array($fields) && isset($fields['pid'])) {
            return (int) $fields['pid'];
        }

        // Fallback (used for deleted records)
        list($pid) = BackendUtility::getTSCpid($table, $id, '');

        return (int) $pid;
    }

    /**
     * Check if one of our watched fields have been changed.
     *
     * @param array $fields
     * @param array $watchedFields
     *
     * @return bool
     */
    protected function isUpdateNeeded($fields, $watchedFields)
    {
        // If uid field exists (and therefore all fields) nothing has been updated
        if (array_key_exists('uid', $fields)) {
            return false;
        }

        // Check if one of the updated fields is relevant for us
        $changedFields = array_keys($fields);
        if (is_array($changedFields)) {
            $updatedFields = array_intersect($changedFields, $watchedFields);

            if (is_array($updatedFields) && count($updatedFields) > 0) {
                return true;
            }
        }

        return false;
    }
}
