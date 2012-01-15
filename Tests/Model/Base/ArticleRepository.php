<?php

namespace Model\Base;

/**
 * Base class of repository of Model\Article document.
 */
abstract class ArticleRepository extends \Mandango\Repository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Mandango\Mandango $mandango)
    {
        $this->documentClass = 'Model\Article';
        $this->isFile = false;
        $this->collectionName = 'model_article';

        parent::__construct($mandango);
    }

    /**
     * {@inheritdoc}
     */
    public function idToMongo($id)
    {
        if (!$id instanceof \MongoId) {
            $id = new \MongoId($id);
        }

        return $id;
    }

    /**
     * Save documents.
     *
     * @param mixed $documents          A document or an array of documents.
     * @param array $batchInsertOptions The options for the batch insert operation (optional).
     * @param array $updateOptions      The options for the update operation (optional).
     */
    public function save($documents, array $batchInsertOptions = array(), array $updateOptions = array())
    {
        $repository = $this;

        if (!is_array($documents)) {
            $documents = array($documents);
        }

        $identityMap =& $this->getIdentityMap()->allByReference();
        $collection = $this->getCollection();

        $inserts = array();
        $updates = array();
        foreach ($documents as $document) {
            if ($document->isNew()) {
                $inserts[spl_object_hash($document)] = $document;
            } else {
                $updates[] = $document;
            }
        }

        // insert
        if ($inserts) {
            $a = array();
            foreach ($inserts as $oid => $document) {
                $a[$oid] = $document->queryForSave();
                $a[$oid]['_id'] = new \MongoId();
            }

            if ($a) {
                $collection->batchInsert($a, $batchInsertOptions);

                foreach ($a as $oid => $data) {
                    $document = $inserts[$oid];

                    $document->setId($data['_id']);
                    $document->setIsNew(false);
                    $document->clearModified();
                    $identityMap[(string) $data['_id']] = $document;
                }
            }
        }

        // updates
        foreach ($updates as $document) {
            if ($document->isModified()) {
                $query = $document->queryForSave();
                $collection->update(array('_id' => $document->getId()), $query, $updateOptions);
                $document->clearModified();
            }
        }
    }

    /**
     * Delete documents.
     *
     * @param mixed $documents A document or an array of documents.
     */
    public function delete($documents)
    {
        if (!is_array($documents)) {
            $documents = array($documents);
        }

        $identityMap =& $this->getIdentityMap()->allByReference();

        $ids = array();
        foreach ($documents as $document) {
            $ids[] = $id = $document->getAndRemoveId();
            $document->setIsNew(true);
            unset($identityMap[(string) $id]);
        }

        $this->getCollection()->remove(array('_id' => array('$in' => $ids)));
    }

    /**
     * Ensure the inexes.
     */
    public function ensureIndexes()
    {
    }
}