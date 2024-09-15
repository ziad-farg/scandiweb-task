<?php

namespace App\Core;

use App\Core\Traits\ValidationHelper;

class Validator
{
    use ValidationHelper;

    public static function validate($roles, $data = []): self
    {
        $instance = new self();
        $instance->checkInputs($roles, $data);
        return $instance;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }
}
