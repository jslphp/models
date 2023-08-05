<?php

namespace Jsl\Models\Components;

use Jsl\Models\Attributes\Column;
use Jsl\Models\Attributes\Schema;
use Jsl\Models\Schema\Model;
use Jsl\Models\Schema\Property;
use ReflectionClass;
use ReflectionProperty;

class Attributes
{
    /**
     * @param string $modelClass
     *
     * @return Model|null
     */
    public function fromModel(string $class): ?Model
    {
        $refClass = new ReflectionClass($class);
        $model = $this->getModelScema($refClass, $class);

        if ($model === null) {
            return null;
        }

        foreach ($refClass->getProperties() as $refProp) {
            if ($property = $this->getModelProperty($refProp)) {
                $model->add($property);
            }
        }

        return $model;
    }


    /**
     * @param ReflectionClass $refClass
     * @param string $class
     *
     * @return Model|null
     */
    protected function getModelScema(ReflectionClass $refClass, string $class): ?Model
    {
        $table = null;
        foreach ($refClass->getAttributes(Schema::class) as $attr) {
            $table = $attr->newInstance()->table;
        }

        return $table ? new Model($class, $table) : null;
    }


    /**
     * @param ReflectionProperty $refProp
     *
     * @return Property|null
     */
    protected function getModelProperty(ReflectionProperty $refProp): ?Property
    {
        $property = null;
        foreach ($refProp->getAttributes(Column::class) as $refAttr) {
            $attribute = $refAttr->newInstance();

            $property = new Property(
                $refProp->getName(),
                $attribute->column ?: $refProp->getName()
            );

            $property->setFromAttribute($attribute);
        }

        return $property;
    }
}
