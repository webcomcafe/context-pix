<?php

namespace Webcomcafe\Pix\Resources;

interface ResourceInterface
{
    public function create(array $data);

    public function all(array $data = []);

    public function find(array $data);

    public function update(array $data);

    public function change(array $data);

    public function remove(array $data);
}