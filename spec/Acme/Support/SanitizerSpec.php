<?php namespace spec\Acme\Support;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SanitizerSpec extends ObjectBehavior {

    function let()
    {
        $this->beAnInstanceOf('spec\Acme\Support\TestSanitizer');
    }

    function it_sanitizes_data_against_a_set_of_rules()
    {
        $this->sanitize(
            ['slug' => 'SOME-SLUG'],
            ['slug' => 'strtolower']
        )->shouldReturn(['slug' => 'some-slug']);

        $this->sanitize(
            ['first' => 'john'],
            ['first' => 'ucwords', 'last' => 'ucwords']
        )->shouldReturn(['first' => 'John']);
    }

    function it_can_apply_multiple_sanitizers()
    {
        $this->sanitize(
            ['name' => '  john doe  '],
            ['name' => 'trim|ucwords']
        )->shouldReturn(['name' => 'John Doe']);
    }

    function it_allows_sanitizers_to_optionally_be_an_array()
    {
        $this->sanitize(
            ['name' => '  john doe  '],
            ['name' => ['trim', 'ucwords']]
        )->shouldReturn(['name' => 'John Doe']);
    }

    function it_fetches_rules_off_of_a_subclass_if_they_are_not_passed_in()
    {
        $this->sanitize(['name' => '   john'])->shouldReturn(['name' => 'John']);
    }

    function it_allows_for_custom_sanitization()
    {
        $this->sanitize(['phone' => '555-555-5555'])->shouldReturn(['phone' => '5555555555']);
    }


}

class TestSanitizer extends \Acme\Support\Sanitizer {
    protected $rules = [
        'name' => 'trim|ucwords',
        'phone' => 'phone'
    ];

    function sanitizePhone($value)
    {
        return str_replace('-', '', $value);
    }
}