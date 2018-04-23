<?php

namespace Epignosis\TalentCards;

use Epignosis\TalentCards\Traits\RestfulTrait;

class Account extends AbstractObject
{
    use RestfulTrait;

    protected $endpoint = '';

    protected $wrapper = 'data';

    /**
     * Get all Users of Account
     * GET /company/users/
     *
     * @return mixed
     */
    public function users()
    {
        $this->endpoint = '/users';

        return $this;
    }

    /**
     * Get All Groups of Account
     * GET /company/groups/
     *
     * @return mixed
     */
    public function groups()
    {
        $this->endpoint = '/groups';

        return $this;
    }

    /**
     * Get All Sets of Account
     * GET /company/sets/
     *
     * @return mixed
     */
    public function sets()
    {
        $this->endpoint = '/sets';

        return $this;
    }

    /**
     * Get All Set-Sequences of Account
     * GET /company/users/
     *
     * @return mixed|array
     */
    public function sequences()
    {
        $this->endpoint = '/sequences';

        return $this;
    }

    /**
     * Get Account Info
     * GET /company
     *
     * @return mixed|array
     */
    public function info()
    {
        return $this->client->get('/')->response();
    }

    /**
     * Get Authenticated API User
     * GET /company/me
     *
     * @return mixed|array
     */
    public function me()
    {
        return $this->client->get('/me')->response();
    }
}