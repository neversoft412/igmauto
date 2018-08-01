<?php

namespace Application\Hydrator\Strategy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Util\Inflector;
use DoctrineModule\Stdlib\Hydrator\Strategy\AllowRemoveByValue as DoctrineAllowRemoveByValue;
use LogicException;

class AllowRemoveByValue extends DoctrineAllowRemoveByValue
{

    /**
     * @param string|null        $collectionName
     * @param ClassMetadata|null $metadata
     */
    public function __construct($collectionName = null, $metadata = null)
    {
        $this->collectionName = $collectionName;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value)
    {
        // AllowRemove strategy need "adder" and "remover"
        $adder = 'add' . ucfirst(Inflector::singularize($this->collectionName));
        $remover = 'remove' . ucfirst(Inflector::singularize($this->collectionName));

        if (!method_exists($this->object, $adder) || !method_exists($this->object, $remover)) {
            throw new LogicException(
                sprintf(
                    'AllowRemove strategy for DoctrineModule hydrator requires both %s and %s to be defined in %s
                     entity domain code, but one or both seem to be missing',
                    $adder,
                    $remover,
                    \get_class($this->object)
                )
            );
        }

        $collection = $this->getCollectionFromObjectByValue();

        if ($collection instanceof Collection) {
            $collection = $collection->toArray();
        }

        $toAdd = new ArrayCollection(array_udiff($value, $collection, [$this, 'compareObjects']));
        $toRemove = new ArrayCollection(array_udiff($collection, $value, [$this, 'compareObjects']));

        foreach ($toAdd as $item) {
            $this->object->{$adder}($item);
        }

        foreach ($toRemove as $item) {
            $this->object->{$remover}($item);
        }

        return $collection;
    }
}
