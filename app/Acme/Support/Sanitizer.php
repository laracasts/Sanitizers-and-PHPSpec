<?php namespace Acme\Support;

abstract class Sanitizer {

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function sanitize(array $data, array $rules = null)
    {
        $rules = $rules ?: $this->getRules();

        foreach ($rules as $field => $sanitizers)
        {
            if ( ! isset($data[$field])) continue;

            $data[$field] = $this->applySanitizersTo($data[$field], $sanitizers);
        }

        return $data;
    }

    /**
     * @param array $value
     * @param $sanitizers
     * @return string
     */
    private function applySanitizersTo($value, $sanitizers)
    {
        foreach ($this->splitSanitizers($sanitizers) as $sanitizer)
        {
            $method = 'sanitize'.ucwords($sanitizer);

            // If a custom sanitizer is registered on the subclass,
            // then let's trigger that instead.
            $value = method_exists($this, $method)
                ? call_user_func([$this, $method], $value)
                : call_user_func($sanitizer, $value);
        }

        return $value;
    }

    /**
     * @param $sanitizers
     * @return array
     */
    private function splitSanitizers($sanitizers)
    {
        return is_array($sanitizers) ? $sanitizers : explode('|', $sanitizers);
    }

}
