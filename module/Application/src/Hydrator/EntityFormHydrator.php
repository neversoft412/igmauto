<?php

namespace Application\Hydrator;

use Application\Hydrator\Strategy\AllowRemoveByValue;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;
use DoctrineModule\Stdlib\Hydrator\Strategy\AllowRemoveByReference;
use InvalidArgumentException;
use Zend\Hydrator\Filter\FilterProviderInterface;

class EntityFormHydrator extends DoctrineObject
{

    /**
     * {@inheritdoc}
     */
    protected function prepareStrategies(): void
    {
        $associations = $this->metadata->getAssociationNames();

        foreach ($associations as $association) {
            if ($this->metadata->isSingleValuedAssociation($association)) {
                // Add a strategy if the association has none set by user
                // if ($this->byValue && !$this->hasStrategy($association)) {
                //     $this->addStrategy($association, new SingleValuedAssociationByValue());
                // }
            } elseif ($this->metadata->isCollectionValuedAssociation($association)) {
                // Add a strategy if the association has none set by user
                if (!$this->hasStrategy($association)) {
                    if ($this->byValue) {
                        $this->addStrategy($association, new AllowRemoveByValue());
                    } else {
                        $this->addStrategy($association, new AllowRemoveByReference());
                    }
                }

                $strategy = $this->getStrategy($association);

                if (!$strategy instanceof AbstractCollectionStrategy) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Strategies used for collections valued associations must inherit from '
                            . 'Strategy\AbstractCollectionStrategy, %s given',
                            \get_class($strategy)
                        )
                    );
                }

                $strategy->setCollectionName($association)
                    ->setClassMetadata($this->metadata);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function extractByValue($object): array
    {
        $data = parent::extractByValue($object);

        $fieldNames = array_merge(
            $this->metadata->getFieldNames(),
            $this->metadata->getAssociationNames()
        );

        $filter = $object instanceof FilterProviderInterface
            ? $object->getFilter()
            : $this->filterComposite;

        foreach ($fieldNames as $fieldName) {
            if ($filter && !$filter->filter($fieldName)) {
                continue;
            }

            $dataFieldName = $this->computeExtractFieldName($fieldName);
            if (array_key_exists($dataFieldName, $data)) {
                continue;
            }

            if (\is_callable([$object, $fieldName])) {
                $data[$dataFieldName] = $this->extractValue(
                    $fieldName,
                    $object->{$fieldName}(),
                    $object
                );
            }
        }

        return $data;
    }

    /**
     * @param array  $data
     * @param object $object
     *
     * @return object
     */
    protected function hydrateByValue(array $data, $object)
    {
        $data = $this->convertTypes($data);
        $object = parent::hydrateByValue($data, $object);

        $metadata = $this->metadata;

        foreach ($data as $field => $value) {
            $field = $this->computeHydrateFieldName($field);
            $value = $this->handleTypeConversions($value, $metadata->getTypeOfField($field));

            if (!$metadata->hasField($field) && !$metadata->hasAssociation($field) && $this->hasStrategy($field)) {
                $strategy = $this->getStrategy($field);

                if ($strategy instanceof AbstractCollectionStrategy) {
                    if (\is_array($value)) {
                        $this->toMany(
                            $object,
                            $strategy->getCollectionName(),
                            $strategy->getClassMetadata()->getName(),
                            $value
                        );
                    } else {
                        $setter = 'set' . Inflector::classify($field);

                        if (\is_callable([$object, $setter])) {
                            $value = $this->toOne(
                                $strategy->getClassMetadata()->getName(),
                                $this->hydrateValue($field, $value, $data)
                            );
                            $object->$setter($value);
                        }
                    }
                }
            }
        }

        return $object;
    }

    /**
     * @param array  $data
     * @param object $object
     *
     * @return object
     */
    protected function hydrateByReference(array $data, $object)
    {
        $data = $this->convertTypes($data);

        return parent::hydrateByReference($data, $object);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertTypes(array $data): array
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $this->metadata;

        foreach ($data as $field => $value) {
            $computedField = $this->computeHydrateFieldName($field);

            $isNullable = false;
            if ($metadata->hasField($computedField)) {
                $isNullable = $metadata->isNullable($computedField);
            } elseif ($metadata->hasAssociation($computedField)
                && $metadata->isSingleValuedAssociation($computedField)
            ) {
                $associationMapping = $metadata->getAssociationMapping($computedField);
                $isNullable = (bool)$associationMapping['joinColumns'][0]['nullable'];
            }

            $data[$field] = $this->handleTypeConversions(
                $value,
                $metadata->getTypeOfField($computedField),
                $isNullable,
                false
            );
        }

        return $data;
    }

    /**
     * @param mixed  $value
     * @param string $typeOfField
     * @param bool   $isNullable
     * @param bool   $skip
     *
     * @return mixed
     */
    protected function handleTypeConversions($value, $typeOfField, $isNullable = true, $skip = true)
    {
        if ($skip === false) {
            switch ($typeOfField) {
                case 'date':
                case 'datetime':
                case 'datetimetz':
                case 'time':
                    if (($value === '' || $value === null) && $isNullable) {
                        return null;
                    }

                    if (\is_int($value)) {
                        $dateTime = new \DateTime();
                        $dateTime->setTimestamp($value);
                        $value = $dateTime;
                    } elseif (\is_string($value)) {
                        $value = new \DateTime($value);
                    }
                    break;

                case 'float':
                    if (($value === '' || $value === null) && $isNullable) {
                        return null;
                    }

                    $value = (float)$value;
                    break;

                case 'boolean':
                    if (($value === '' || $value === null) && $isNullable) {
                        return null;
                    }

                    $value = (bool)$value;
                    break;

                case 'integer':
                case 'smallint':
                case 'bigint':
                    if (($value === '' || $value === null) && $isNullable) {
                        return null;
                    }

                    $value = (int)$value;
                    break;

                case 'string':
                case 'text':
                case 'decimal':
                case 'money':
                    if (($value === '' || $value === null) && $isNullable) {
                        return null;
                    }

                    $value = (string)$value;
                    break;
            }
        }

        return $value;
    }
}
