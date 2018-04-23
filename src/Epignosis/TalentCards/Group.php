<?php

namespace Epignosis\TalentCards;

use Epignosis\TalentCards\Traits\RestfulTrait;

/**
 * Class Group
 *
 * @package Epignosis\TalentCards
 */
class Group extends AbstractObject
{
    use RestfulTrait;

    protected $endpoint = '/groups';

    protected $wrapper = 'data';

    /**
     * Get all Users of Group
     * GET /company/groups/{group}/users
     *
     * @return mixed
     */
    public function users()
    {
        $this->endpoint = "/groups/{$this->getId()}/users";

        return $this;
    }

    public function reportsFor($user_id)
    {
        $this->endpoint = "/groups/{$this->getId()}/users/{$user_id}/reports";

        return $this->client->get($this->endpoint, [])->response();
    }

    public function enrollToSets($user_id, $data)
    {
        $this->endpoint = "/groups/{$this->getId()}/users/{$user_id}/relationships/sets";

        return $this->client->post($this->endpoint, [$this->wrapper => $data])->response();
    }

    public function disenrollFromSets($user_id, $data)
    {
        $this->endpoint = "/groups/{$this->getId()}/users/{$user_id}/relationships/sets";

        return $this->client->delete($this->endpoint, [$this->wrapper => $data])->response();
    }

    /**
     * Get All Sets of Group
     * GET /company/groups/{group}/sets/
     *
     * @return mixed
     */
    public function sets()
    {
        $this->endpoint = "/groups/{$this->getId()}/sets";

        return $this;
    }

    /**
     * Get All Set Sequences of Group
     * GET /company/groups/{group}/sequences
     *
     * @return mixed
     */
    public function sequences()
    {
        $this->endpoint = "/groups/{$this->getId()}/sequences";

        return $this;
    }
}