<?php

namespace Epignosis\TalentCards\Contracts;

interface RestfulInterface
{
    public function all();

    public function create($data);

    public function update($data);

    public function find();
}