<?php

namespace App\Services\Support;

class ServiceResponse
{
    public function __construct(
        public array $data = ['message' => 'success'],
        public int $code = 200,
    ) {}
}
