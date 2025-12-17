<?php

namespace XMVC\Service;

/**
 * Service for validating data against a set of rules.
 */
class Validator
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The validation errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Create a new Validator instance.
     *
     * @param array $data The data to validate.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate the data against the given rules.
     *
     * @param array $rules An associative array of rules (e.g., ['email' => 'required|email']).
     *
     * @return void
     */
    public function validate(array $rules)
    {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }

    /**
     * Apply a specific rule to a field.
     *
     * @param string $field The field name.
     * @param string $rule  The rule definition (e.g., 'required', 'min:5').
     *
     * @return void
     */
    protected function applyRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;

        if ($rule === 'required') {
            if (empty($value) && $value !== '0') {
                $this->errors[$field][] = "The {$field} field is required.";
            }
        }

        if (strpos($rule, 'min:') === 0) {
            $min = substr($rule, 4);
            if (strlen($value) < $min) {
                $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
            }
        }

        if (strpos($rule, 'max:') === 0) {
            $max = substr($rule, 4);
            if (strlen($value) > $max) {
                $this->errors[$field][] = "The {$field} may not be greater than {$max} characters.";
            }
        }

        if ($rule === 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                 $this->errors[$field][] = "The {$field} must be a valid email address.";
            }
        }
    }

    /**
     * Determine if the validation failed.
     *
     * @return bool True if there are errors, false otherwise.
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Get the validation errors.
     *
     * @return array An associative array of errors.
     */
    public function errors()
    {
        return $this->errors;
    }
}