<?php

namespace Epignosis\TalentCards;

use Epignosis\TalentCards\Contracts\RequestableInterface;

abstract class AbstractObject
{
    /**
     * @var RequestableInterface
     */
    protected $client;

    /**
     * @var null|integer
     */
    protected $id;

    /**
     * @param RequestableInterface $client
     * @param null $id
     */
    public function __construct(RequestableInterface $client, $id = null)
    {
        $this->client = $client;
        $this->id     = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
