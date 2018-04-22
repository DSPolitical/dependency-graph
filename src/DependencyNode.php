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

    private $guid;

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
        $this->guid = $this->generateGUID();
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

    /*
     * Recursively search the hierarchy for a dependency object with the appropriate
     * class name. By default, searches just for the (unqualified) class name _or_
     * the unqualified classname without the string "Node".
     *
     * E.g. searching for "LandingPage" will find any ancestors of class
     * "LandingPage" or "LandingPageNode"
     */
    public function getDependenciesByName($name, $useShortName = true)
    {
        if (count($this->dependencies) === 0)
            return [];
        $matches = [];

        foreach ($this->dependencies as $dependency)
        {
            if ($useShortName)
                $parentName = (new \ReflectionClass($dependency))->getShortName();
            else
                $parentName = get_class($dependency);

            if ($parentName === $name || str_replace('Node', '', $parentName) === $name)
                $matches[] = $dependency;

            $matches = array_merge($matches, $dependency->getDependenciesByName($name, $useShortName));
        }

        return array_unique($matches);
    }

    public function getDepth()
    {
        if (count($this->dependencies) === 0)
            return 0;

        $depth = 0;
        foreach ($this->dependencies as $dependency)
        {
            $dependencyDepth = $dependency->getDepth();
            if ($dependencyDepth > $depth)
                $depth = $dependencyDepth;
        }
        return $depth + 1;
    }

    private function generateGUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function getGUID()
    {
        return $this->guid;
    }
}
