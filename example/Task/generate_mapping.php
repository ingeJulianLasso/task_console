<?php
use TaskConsole\Interfaces\ITask;

class generate_mapping implements ITask
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function task(): void
    {
        print_r($this->params);
    }
}