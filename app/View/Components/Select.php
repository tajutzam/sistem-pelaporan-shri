<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $label;
    public string $name;
    public array $options;
    public ?string $selected;

    public function __construct(
        string $label,
        string $name,
        array $options = [],
        string $selected = null
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
    }

    public function render()
    {
        return view('components.select');
    }
}
