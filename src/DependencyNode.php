<?php

namespace DSPolitical\DependencyGraph;

/**
 * This class describes a node in a dependency graph.
 */
class DependencyNode
{

    /**
     * @var mixed
     */
    private $name;

    /**
     * @var mixed
     */
    private $element;

    /**
     * @var array
     */
    private $dependencies = array();

    protected $parent;

    /**
     * Create a new node for the dependency graph. The passed element can be an object or primitive,
     * it doesn't matter, as the resolving is based on nodes.
     *
     * Optionally you can pass a specific name, which will help you if circular dependencies are detected.
     *
     * @param string $name
     * @param mixed $element
     */
    public function __construct($element = null, $name = null)
    {
        $this->element = $element;
        $this->name = $name;
        $this->parent = null;
    }

    /**
     * This node as a dependency on the passed node.
     *
     * @param DependencyNode $node
     */
    public function dependsOn(self $node)
    {
        if (!in_array($node, $this->dependencies)) {
            $this->dependencies[] = $node;
        }
        $this->parent = $node;
    }

    /**
     * @return DependencyNode[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param mixed$element
     *
     * @return $this
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    /*
     * Recursively search the hierarchy for a parent object with the appropriate
     * class name. By default, searches just for the (unqualified) class name _or_
     * the unqualified classname without the string "Node".
     *
     * E.g. searching for "LandingPage" will find any ancestors of class
     * "LandingPage" or "LandingPageNode"
     */
    public function getParentByName($name, $useShortName = true)
    {
        var_dump($name);
        if ($this->getParent() === null)
            return null;
        else
        {
            // Short
            if ($useShortName)
            {
                $parentShortName = (new \ReflectionClass($this->getParent()))->getShortName();
                if ($parentShortName === $name || str_replace('Node','',$parentShortName) === $name)
                    return $this->getParent();
            }
            else
            {
                if ($this->getParent() instanceof $name)
                    return $this->getParent();
            }
            return $this->getParent()->getParentByName($name, $useShortName);
        }
    }
}
