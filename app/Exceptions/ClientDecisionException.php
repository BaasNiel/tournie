<?php

namespace App\Exceptions;

use Exception;

class ClientDecisionException extends Exception
{
    /**
     * Any extra data to send with the response.
     *
     * @var array
     */
    public $data = [];

    /**
     * The status code to use for the response.
     *
     * @var integer
     */
    public $status = 204;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message, $data)
    {
        parent::__construct($message);

        $this->data = $data;
    }

    public function render($request): array
    {
        $this->data['message'] = $this->getMessage();
        return [
            'success' => false,
            'data'    => $this->data
        ];
    }
}
