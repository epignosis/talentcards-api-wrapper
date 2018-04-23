<?php

namespace Epignosis\TalentCards\Traits;

trait RestfulTrait
{
    /**
     * @param $parameters
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        return $this->client->get($this->endpoint, $parameters)->response();
    }

    /**
     * @param $id
     * @param array $parameters
     * @return mixed
     */
    public function find($id, array $parameters = [])
    {
        return $this->client->get("$this->endpoint/$id", $parameters)->response();
    }

    /**
     * @param $data
     * @return string
     */
    public function create($data)
    {
        return $this->client->post($this->endpoint, [$this->wrapper => $data])->response();
    }

    public function related($type, $id, $parameters)
    {
        return $this->client->get("$this->endpoint/{$id}/relationships/$type", $parameters)->response();
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
        return $this->client->patch("$this->endpoint/{$id}", [$this->wrapper => $data])->response();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function replace($data)
    {
        return $this->client->put("$this->endpoint/$this->id", [$this->wrapper => $data])->response();
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->client->delete("$this->endpoint/$this->id")->response();
    }
}