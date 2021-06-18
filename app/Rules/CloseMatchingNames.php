<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CloseMatchingNames implements Rule
{
    private $first_name;
    private $confirm_name;
    private $closeMatchingNames;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($first_name, $confirm_name)
    {
        $this->first_name = $first_name;
        $this->confirm_name = ($confirm_name == "true");
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->closeMatchingNames = $this->getCloseMatchingNames($value);
        if (empty($this->closeMatchingNames->toArray()) or $this->confirm_name) {
            return true;
        }
        request()->session()->flash('closeMatchingNames', $this->matchingNames());
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Do you mean one of the existing members below...';
    }

    private function getCloseMatchingNames($value) {
        return \App\Person::closeMatchingNames($this->first_name, $value)
            ->select('id', 'first_name', 'last_name', 'name')
            ->get();
    }

    private function matchingNames() {
        $matchingNames = [];
        foreach ($this->closeMatchingNames as $name) {
            $matchingNames[] = ['id' => $name->id, 'name' => $name->name];
        }
        return $matchingNames;
    }
}
