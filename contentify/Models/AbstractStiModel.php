<?php

namespace Contentify\Models;

/**
 * Single Table Inheritance Model. @see https://en.wikipedia.org/wiki/Single_Table_Inheritance
 * It differs from common implementations: It does not have to use the class name 
 * but may use a custom value to link a PHP class to an database record.
 * The subclass models may set their $subclassId value to link themselves to a superclass
 * if they don't want to use their class names.
 */
abstract class AbstractStiModel extends BaseModel
{
    /**
     *
     * The name of the field that stores the subclass
     *
     * @var string
     */
    protected $subclassField = null;

    /**
     * Indicates if the inheriting class is a subclass
     *
     * @var bool
     */
    protected $isSubclass = false;

    /**
     * ID value of the subclass.
     * If null, use the name of the class.
     *
     * @var string|int|null
     */
    protected $subclassId = null;

    /**
     * Returns true if class is subclass
     * 
     * @return bool
     */
    public function isSubclass() : bool
    {
        return $this->isSubclass;
    }

    /**
     * AbstractStiModel constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->subclassField and $this->isSubclass()) {
            if ($this->subclassId) {
                $value = $this->subclassId;
            } else {
                $value = class_basename(get_class($this));
            }

            $this->setAttribute($this->subclassField, $value);
        }
    }

    /**
     * Returns an instance of the concrete (extending) model class
     *
     * @return self
     */
    public function getStiClassInstance() : self
    {
        $className = get_class($this); // The name of the concrete (extending) model class

        return new $className;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if ($connection) {
            throw new \Exception('Error: STI model does not support any connection parameter.');
        }

        /*
         * Instead of using $this->newInstance(), call
         * newInstance() on the object from mapData
         */
        $instance = $this->getStiClassInstance()->newInstance([], true);
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function newQuery()
    {
        $builder = parent::newQuery();

        /*
         * If this is a subclass, add a condition to the query.
         * Use $subclassId as value or if it is null, the class name.
         */ 
        if ($this->subclassField and $this->isSubclass()) {
            if ($this->subclassId) {
                $value = $this->subclassId;
            } else {
                $value = class_basename(get_class($this));
            }

            $builder->where($this->subclassField, '=', $value);
        }

        return $builder;
    }
}
