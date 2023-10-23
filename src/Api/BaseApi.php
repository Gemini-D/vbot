<?php

declare(strict_types=1);

namespace Hanson\Vbot\Api;

use Hanson\Vbot\Foundation\Vbot;

abstract class BaseApi
{
    public function __construct(protected Vbot $vbot)
    {
    }

    public function validate($params)
    {
        if ($diff = array_diff($this->needParams(), array_keys($params))) {
            return $this->response('params : \'' . implode('\', \'', $diff) . '\' missing.', 500);
        }

        return true;
    }

    public function execute($params): array
    {
        if (is_array($result = $this->validate($params))) {
            return $result;
        }

        return $this->handle($params);
    }

    abstract public function needParams(): array;

    abstract public function handle($params): array;

    protected function response($result = [], $code = 200): array
    {
        return ['code' => $code, 'result' => $result];
    }
}
