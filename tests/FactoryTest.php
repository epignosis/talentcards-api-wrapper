<?php

use Epignosis\TalentCards\Client;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var Epignosis\TalentCards\Factory $factory
     */
    protected $factory;

    public function setup()
    {
        $this->factory = new \Epignosis\TalentCards\Factory(new Client());
    }

    /** @test */
    public function it_can_tell_if_a_not_existing_method_is_called()
    {
        $this->setExpectedException('Epignosis\TalentCards\Exceptions\ClassNotCreatedException');
        $this->factory->notexisting();
    }

    /** @test
     */
    public function it_can_tell_if_an_existing_method_is_called()
    {
        $this->assertInstanceOf('Epignosis\TalentCards\AbstractObject', $this->factory->account());
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}