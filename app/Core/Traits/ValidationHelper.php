<?php


namespace App\Core\Traits;

use PDO;
use App\Core\Database\Database;

trait ValidationHelper
{
    protected array $errors = [];

    private $_regexPatterns = [
        'num' => '/^[0-9]+(?:\.[0-9]+)?$/',
        'int' => '/^[0-9]+$/',
        'float' => '/^[0-9]+\.[0-9]+$/',
        'alpha' => '/^[a-zA-Z ]+$/u',
        'alphanum' => '/^[a-zA-Z0-9]+$/u',
        'vdate' => '/^[1-2][0-9][0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:0[1-9])|(?:(?:1|2)[0-9])|(?:3[0-1]))$/',
        'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'url' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
    ];

    public function req($value)
    {
        return '' != $value || !empty($value);
    }

    public function reqif($value, $field, $fieldValue)
    {

        if ($field == $fieldValue) {
            return '' != $value || !empty($value);
        }
        return true;
    }

    public function db_has($value, $tableName, $fieldName)
    {
        /**
         * @var PDO $db
         */
        $db = Database::getConnection();
        $sql = "SELECT `$fieldName` FROM `$tableName` WHERE `$fieldName` = :value";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':value', $value);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return empty($data);
        }
    }

    public function num($value)
    {
        return (bool)preg_match($this->_regexPatterns['num'], $value);
    }

    public function int($value)
    {
        return (bool)preg_match($this->_regexPatterns['int'], $value);
    }

    public function float($value)
    {
        return (bool)preg_match($this->_regexPatterns['float'], $value);
    }

    public function alpha($value)
    {
        return (bool)preg_match($this->_regexPatterns['alpha'], $value);
    }

    public function alphanum($value)
    {
        return (bool)preg_match($this->_regexPatterns['alphanum'], $value);
    }

    public function eq($value, $matchAgainst)
    {
        return $value == $matchAgainst;
    }

    public function eq_field($value, $otherFieldValue)
    {
        return $value == $otherFieldValue;
    }

    public function lt($value, $matchAgainst)
    {
        if (is_string($value)) {
            return strlen($value) < $matchAgainst;
        } elseif (is_numeric($value)) {
            return $value < $matchAgainst;
        }
    }

    public function gt($value, $matchAgainst)
    {
        if (is_string($value)) {
            return strlen($value) > $matchAgainst;
        } elseif (is_numeric($value)) {
            return $value > $matchAgainst;
        }
    }

    public function min($value, $min)
    {
        if (is_string($value)) {
            return strlen($value) >= $min;
        } elseif (is_numeric($value)) {
            return $value >= $min;
        }
    }

    public function max($value, $max)
    {
        if (is_string($value)) {
            return strlen($value) <= $max;
        } elseif (is_numeric($value)) {
            return $value <= $max;
        }
    }

    public function between($value, $min, $max): bool
    {
        if (is_string($value)) {
            return strlen($value) >= $min && strlen($value) <= $max;
        } elseif (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }
    }

    public function in($value, $str): bool
    {
        return in_array($value, explode(',', $str));
    }

    public function floatlike($value, $beforeDP, $afterDP): bool
    {
        if (!$this->float($value)) {
            return false;
        }
        $pattern = '/^[0-9]{' . $beforeDP . '}\.[0-9]{' . $afterDP . '}$/';
        return (bool)preg_match($pattern, $value);
    }

    public function vdate($value): bool
    {
        return (bool)preg_match($this->_regexPatterns['vdate'], $value);
    }

    public function email($value): bool
    {
        return (bool)preg_match($this->_regexPatterns['email'], $value);
    }

    public function url($value): bool
    {
        return (bool)preg_match($this->_regexPatterns['url'], $value);
    }

    public function checkInputs($roles, $inputType): void
    {
        if (!empty($roles)) {
            $messages = $this->validationMessages();
            foreach ($roles as $fieldName => $validationRoles) {
                $value = $inputType[$fieldName] ?? '';
                $validationRoles = explode('|', $validationRoles);
                foreach ($validationRoles as $validationRole) {
                    if (array_key_exists($fieldName, $this->errors))
                        continue;
                    if (preg_match_all('/(min)\((\d+)\)/', $validationRole, $m)) {
                        if ($this->min($value, $m[2][0]) === false) {
                            $this->errors[$fieldName] = true;
                        }
                    } elseif (preg_match_all('/(max)\((\d+)\)/', $validationRole, $m)) {
                        if ($this->max($value, $m[2][0]) === false) {
                            $this->errors[$fieldName] = true;
                        }
                    } elseif (preg_match_all('/(lt)\((\d+)\)/', $validationRole, $m)) {
                        if ($this->lt($value, $m[2][0]) === false) {
                            $this->errors[$fieldName] = true;
                        }
                    } elseif (preg_match_all('/(gt)\((\d+)\)/', $validationRole, $m)) {
                        if ($this->gt($value, $m[2][0]) === false) {
                            $this->errors[$fieldName] = true;
                        }
                    } elseif (preg_match_all('/(in):(.*)/', $validationRole, $m)) {
                        if ($this->in($value, $m[2][0]) === false) {
                            $this->errors[$fieldName] = sprintf($messages[$m[1][0]], $fieldName, $m[2][0]);
                        }
                    } elseif (preg_match_all('/reqif:(\w+)=(\w+)/', $validationRole, $m)) {
                        if ($this->reqif($value, $inputType[$m[1][0]] ?? '', $m[2][0]) === false) {
                            $this->errors[$fieldName] = sprintf($messages['reqif'], $fieldName);
                        }
                    } elseif (preg_match_all('/exists:(\w+),(\w+)/', $validationRole, $m)) {
                        if ($this->db_has($value, $m[1][0] ?? '', $m[2][0]) === true) {
                            $this->errors[$fieldName] = sprintf($messages['exists'], $fieldName);
                        }
                    } elseif (preg_match_all('/unique:(\w+),(\w+)/', $validationRole, $m)) {
                        if ($this->db_has($value, $m[1][0] ?? '', $m[2][0]) === false) {
                            $this->errors[$fieldName] = sprintf($messages['unique'], $fieldName);
                        }
                    } else {
                        if ($this->$validationRole($value) === false) {
                            $this->errors[$fieldName] = sprintf($messages[$validationRole], $fieldName);
                        }
                    }
                }
            }
        }
    }

    public function validationMessages()
    {
        return [
            'req' => "%s is required",
            'reqif' => "%s is required",
            'num' => "%s must be a number",
            'in' => "%s must be in %s",
            'int' => "%s must be integer",
            'float' => "%s must be in float",
            'alphanum' => "%s must be numbers and strings only",
            'exists' => "The selected %s is invalid.",
            'unique' => "The %s has already been taken.",
        ];
    }
}
