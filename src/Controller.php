<?php

namespace Bubblegum;

use Bubblegum\Routes\RoutedComponent;

class Controller extends RoutedComponent
{
    public function content(Request $request, array $data = []): string
    {
        $response = call_user_func_array([$this, $this->destinationName], func_get_args());
        return match (gettype($response)) {
            'string' => $response,
            default => json_encode($response),
        };
    }
}