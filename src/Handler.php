<?php

/**
 * Copyright 2018 Servicio Nacional de Aprendizaje - SENA
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TaskConsole;

class Handler
{

    private $title;

    private $welcome;

    private $tasks;

    private $argv;

    private $task_path;

    public function __construct(string $title, string $version, string $task_path)
    {
        $this->title = $title;
        $this->welcome = "Welcome to {$title} {$version} Console";
        // $this->tasks = array();
        $this->task_path = $task_path;
        $this->argv = (isset($GLOBALS['argv']) === true) ? $GLOBALS['argv'] : array();
        $this->regenerateArrayArgbv();
    }

    public function run(): void
    {
        $this->clearScreen();
        if (count($this->argv) > 0) {
            if (is_file($this->task_path . $this->argv['command'] . '.php') === true) {
                require_once $this->task_path . $this->argv['command'] . '.php';
                (new $this->argv['command']($this->argv['params']))->task();
            } else {
                echo "La tarea solicitada no existe.";
            }
        } else {
            $this->printRule();
            echo "\n {$this->welcome}\n";
            $this->printRule();
            echo "\n USAGE:\n\n";
            echo "\tphp {$this->title} [--command] [arguments argument:value]\n\n";
            echo " COMMANDS:\n\n";
            $this->printComands();
            $this->printRule();
        }
    }

    protected function registerCommand(string $command, string $description, $shorthand = null): Handler
    {
        $this->tasks[$command]['_description'] = $description;
        if ($shorthand !== null) {
            $this->tasks[$command]['_shorthand'] = $shorthand;
            $this->tasks['_shorthand'][$shorthand] = $command;
        }
        return $this;
    }

    protected function registerArgument(string $command, string $argument, string $description, $shorthand = null): Handler
    {
        $this->tasks[$command]['_arguments'][$argument]['_description'] = $description;
        if ($shorthand !== null) {
            $this->tasks[$command]['_arguments'][$argument]['_shorthand'] = $shorthand;
            $this->tasks[$command]['_shorthand_arguments'][$shorthand] = $argument;
        }
        return $this;
    }

    private function regenerateArrayArgbv(): void
    {
        unset($this->argv[0]);
        $tmpArray = array();
        foreach ($this->argv as $data) {
            /**
             * Si encuentra los : por expresión regular
             * entonces hacer splice al array y quitar - o -- al primer elemento
             * luego meterlos como parte del array con el dato que solicitan
             */
            if (preg_match("/(^--)|(^-)/", $data)) {
                $tmp = preg_replace("/(^--)|(^-)/", "", $data);
                if (preg_match("/-/", $tmp) !== false) {
                    $tmp = preg_replace("/-/", "_", $tmp);
                }
                $tmpArray['command'] = (isset($this->tasks['_shorthand'][$tmp]) === true) ? $this->tasks['_shorthand'][$tmp] : $tmp;
            } else if (preg_match("/:/", $data) !== false) {
                $tmp = explode(":", $data, 2);
                $tmpArray['params'][((isset($this->tasks[$tmpArray['command']]['_shorthand_arguments'][$tmp[0]]) === true) ? $this->tasks[$tmpArray['command']]['_shorthand_arguments'][$tmp[0]] : $tmp[0])] = $tmp[1];
            }
        }
        $this->argv = $tmpArray;
    }

    private function printComands(): void
    {
        if (count($this->tasks) > 0) {
            foreach ($this->tasks as $command => $data) {
                if ($command !== '_shorthand') {
                    $shorthand = (isset($data['_shorthand']) === true) ? "-{$data['_shorthand']} " : null;
                    echo " --{$command}\t\t{$shorthand}{$data['_description']}\n";
                    if (isset($data['_arguments']) === true and count($data['_arguments']) > 0) {
                        echo "\n ARGUMENTS:\n";
                        foreach ($data['_arguments'] as $argument => $data2) {
                            $shorthand = (isset($data2['_shorthand']) === true) ? "{$data2['_shorthand']} " : null;
                            echo " {$argument}\t{$shorthand}{$data2['_description']}\n";
                        }
                        echo "\n";
                    }
                }
            }
        }
    }

    private function clearScreen(): void
    {
        system('cls');
        system('clear');
    }

    private function printRule(): void
    {
        for ($x = 0; $x < 80; $x ++) {
            echo "-";
        }
        // echo "\n";
    }
}