<?php
namespace Search\Model\Filter;

use ArrayIterator;

/**
 * FilterCollection
 */
class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var array List of filter objects
     */
    protected $filters = [];

    /**
     * Adds a filter
     *
     * @param \Search\Model\Filter\FilterInterface $filter Filter
     * @return $this
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[$filter->name()] = $filter;

        return $this;
    }

    /**
     * Checks if a filter is in the collection
     *
     * @param string|\Search\Model\Filter\FilterInterface
     * @return bool
     */
    public function has($name)
    {
        if ($name instanceof FilterInterface) {
            $name = $name->name();
        }

        return isset($this->filters[$name]);
    }

    /**
     * Removes a filter by name
     *
     * @param string $name Name of the filter
     * @return $this
     */
    public function remove($name)
    {
        unset($this->filters[$name]);
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->filters);
    }

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset)
	{
		if ($this->has($offset)) {
			return $this->filters[$offset];
		}

		return null;
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value)
	{
		$this->filters[$offset] = $value;
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	public function toArray() {
		return $this->filters;
	}
}
