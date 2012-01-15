<?php

namespace Model\Base;

/**
 * Base class of Model\Article document.
 */
abstract class Article extends \Mandango\Document\Document
{
    /**
     * Initializes the document defaults.
     */
    public function initializeDefaults()
    {
    }

    /**
     * Set the document data (hydrate).
     *
     * @param array $data  The document data.
     * @param bool  $clean Whether clean the document.
     *
     * @return Model\Article The document (fluent interface).
     */
    public function setDocumentData($data, $clean = false)
    {
        if ($clean) {
            $this->data = array();
            $this->fieldsModified = array();
        }

        if (isset($data['_query_hash'])) {
            $this->addQueryHash($data['_query_hash']);
        }
        if (isset($data['_id'])) {
            $this->setId($data['_id']);
            $this->setIsNew(false);
        }
        if (isset($data['title'])) {
            $this->data['fields']['title'] = (string) $data['title'];
        } elseif (isset($data['_fields']['title'])) {
            $this->data['fields']['title'] = null;
        }

        return $this;
    }

    /**
     * Set the "title" field.
     *
     * @param mixed $value The value.
     *
     * @return Model\Article The document (fluent interface).
     */
    public function setTitle($value)
    {
        if (!isset($this->data['fields']['title'])) {
            if (!$this->isNew()) {
                $this->getTitle();
                if ($value === $this->data['fields']['title']) {
                    return $this;
                }
            } else {
                if (null === $value) {
                    return $this;
                }
                $this->fieldsModified['title'] = null;
                $this->data['fields']['title'] = $value;
                return $this;
            }
        } elseif ($value === $this->data['fields']['title']) {
            return $this;
        }

        if (!isset($this->fieldsModified['title']) && !array_key_exists('title', $this->fieldsModified)) {
            $this->fieldsModified['title'] = $this->data['fields']['title'];
        } elseif ($value === $this->fieldsModified['title']) {
            unset($this->fieldsModified['title']);
        }

        $this->data['fields']['title'] = $value;

        return $this;
    }

    /**
     * Returns the "title" field.
     *
     * @return mixed The $name field.
     */
    public function getTitle()
    {
        if (!isset($this->data['fields']['title'])) {
            if ($this->isNew()) {
                $this->data['fields']['title'] = null;
            } elseif (!isset($this->data['fields']) || !array_key_exists('title', $this->data['fields'])) {
                $this->addFieldCache('title');
                $data = $this->getRepository()->getCollection()->findOne(array('_id' => $this->getId()), array('title' => 1));
                if (isset($data['title'])) {
                    $this->data['fields']['title'] = (string) $data['title'];
                } else {
                    $this->data['fields']['title'] = null;
                }
            }
        }

        return $this->data['fields']['title'];
    }

    /**
     * Set a document data value by data name as string.
     *
     * @param string $name  The data name.
     * @param mixed  $vaule The value.
     *
     * @return mixed the data name setter return value.
     *
     * @throws \InvalidArgumentException If the data name is not valid.
     */
    public function set($name, $value)
    {
        if ('title' == $name) {
            return $this->setTitle($value);
        }

        throw new \InvalidArgumentException(sprintf('The document data "%s" is not valid.', $name));
    }

    /**
     * Returns a document data by data name as string.
     *
     * @param string $name The data name.
     *
     * @return mixed The data name getter return value.
     *
     * @throws \InvalidArgumentException If the data name is not valid.
     */
    public function get($name)
    {
        if ('title' == $name) {
            return $this->getTitle();
        }

        throw new \InvalidArgumentException(sprintf('The document data "%s" is not valid.', $name));
    }

    /**
     * Imports data from an array.
     *
     * @param array $data An array.
     *
     * @return {$this->class} The document (fluent interface).
     */
    public function fromArray(array $array)
    {
        if (isset($array['id'])) {
            $this->setId($array['id']);
        }
        if (isset($array['title'])) {
            $this->setTitle($array['title']);
        }

        return $this;
    }

    /**
     * Export the document data to an array.
     *
     * @param Boolean \$withReferenceFields Whether include the fields of references or not (false by default).
     *
     * @return array An array with the document data.
     */
    public function toArray($withReferenceFields = false)
    {
        $array = array('id' => $this->getId());

        $array['title'] = $this->getTitle();

        return $array;
    }

    /**
     * Query for save.
     */
    public function queryForSave()
    {
        $isNew = $this->isNew();
        $query = array();
        $reset = false;

        if (isset($this->data['fields'])) {
            if ($isNew || $reset) {
                if (isset($this->data['fields']['title'])) {
                    $query['title'] = (string) $this->data['fields']['title'];
                }
            } else {
                if (isset($this->data['fields']['title']) || array_key_exists('title', $this->data['fields'])) {
                    $value = $this->data['fields']['title'];
                    $originalValue = $this->getOriginalFieldValue('title');
                    if ($value !== $originalValue) {
                        if (null !== $value) {
                            $query['$set']['title'] = (string) $this->data['fields']['title'];
                        } else {
                            $query['$unset']['title'] = 1;
                        }
                    }
                }
            }
        }
        if (true === $reset) {
            $reset = 'deep';
        }

        return $query;
    }
}