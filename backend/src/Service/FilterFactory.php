<?php

namespace App\Service;

use App\Dto\FilterParams;
use Symfony\Component\HttpFoundation\Request;

class FilterFactory
{
    public function fromRequest(Request $request): FilterParams
    {
        $f = new FilterParams();
        foreach (get_object_vars($f) as $key => $value) {
            if ($request->query->has($key)) {
                $raw = $request->query->get($key);
                $f->$key = is_numeric($raw) ? (str_contains((string)$raw, '.') ? (float)$raw : (int)$raw) : $raw;
            }
        }
        return $f;
    }
}
