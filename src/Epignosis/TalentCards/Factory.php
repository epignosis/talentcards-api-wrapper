<?php

namespace Epignosis\TalentCards;

use Epignosis\TalentCards\Contracts\RequestableInterface;
use Epignosis\TalentCards\Exceptions\ClassNotCreatedException;

/**
 * Class Factory
 *
 * @package Epignosis\TalentCards
 * @method group($group)
 * @method account
 */
class Factory
{
    protected $client;

    /**
     * @param RequestableInterface $client
     */
    public function __construct(RequestableInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws ClassNotCreatedException
     * @throws \ReflectionException
     */
    public function __call($method, $parameters)
    {
        $class = $this->getQualifiedName($method);
        $this->doesClassExist($class);

        if ($this->paramIsId($parameters) === true) {
            return new $class($this->client, $parameters[0]);
        }

        return new $class($this->client);
    }

    /**
     * Get Fully Qualified Name
     *
     * build and return fully qualified name
     * for class to instantiate
     *
     * @param $method
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getQualifiedName($method)
    {
        return $this->getNamespace().'\\'.ucfirst($method);
    }

    /**
     * Get Namespace
     *
     * @return mixed
     * @throws \ReflectionException
     */
    private function getNamespace()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getNamespaceName();
    }

    /**
     *
     * Check if class exists
     *
     * @param $class
     *
     * @throws ClassNotCreatedException
     * @return void
     */
    protected function doesClassExist($class)
    {
        if (! class_exists($class)) {
            throw new ClassNotCreatedException("Class $class could not be created.");
        }
    }

    /**
     * Parameter Has ID
     *
     * is there a parameter being passed in, and is it
     * an integer?
     *
     * @param $parameters
     *
     * @return null|bool
     * @throws \InvalidArgumentException
     */
    protected function paramIsId($parameters)
    {
        if (empty($parameters)) {
            return null;
        }

        if (! \is_numeric($parameters[0])) {
            throw new \InvalidArgumentException('This is not a valid ID');
        }

        return true;
    }
}