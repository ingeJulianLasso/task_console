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

use Codedungeon\PHPCliColors\Color;

class Handler
{
    private $title;

    private $welcome;

    private $tasks;

    private $argv;

    private $task_path;

    private $color;

    public function __construct(string $title, string $version, string $task_path)
    {
        $this->color = new Color();
        $this->title = $title;
        $this->welcome = $this->color->light_white() . "Welcome to " .
            $this->color->bold_green() . $title .
            $this->color->bold_yellow() . " {$version}" .
            $this->color->normal() . $this->color->light_white() . " Console" .
            $this->color->normal();
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
            echo PHP_EOL . " {$this->welcome}" . PHP_EOL;
            $this->printRule();
            echo PHP_EOL . $this->color->yellow() . " USAGE:" . $this->color->normal() . PHP_EOL . PHP_EOL;
            echo "\tphp {$this->title} [--command] [arguments argument:value]" . PHP_EOL;
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
                if (isset($this->tasks['_shorthand'][$tmp]) === true) {
                    $tmpArray['command'] = $this->tasks['_shorthand'][$tmp];
                } else {
                    $tmpArray['command'] = $tmp;
                }
            } elseif (preg_match("/:/", $data) !== false) {
                $tmp = explode(":", $data, 2);
                $tmpArray['params'][
                    ((isset($this->tasks[$tmpArray['command']]['_shorthand_arguments'][$tmp[0]]) === true)
                    ? $this->tasks[$tmpArray['command']]['_shorthand_arguments'][$tmp[0]] : $tmp[0])
                ] = $tmp[1];
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
                    $command = str_pad(' --' . $command, 25);
                    echo $this->color->yellow() . PHP_EOL . " COMMAND:" . $this->color->normal() . PHP_EOL . PHP_EOL;
                    echo $this->color->green() . $command . $shorthand . $this->color->normal() . $this->color->light_white() . $data['_description'] . PHP_EOL . $this->color->normal();


                    if (isset($data['_arguments']) === true and count($data['_arguments']) > 0) {
                        echo PHP_EOL . $this->color->yellow() . " ARGUMENTS:" . PHP_EOL . PHP_EOL .
                            $this->color->normal();
                        foreach ($data['_arguments'] as $argument => $data2) {
                            $shorthand = (isset($data2['_shorthand']) === true) ? "{$data2['_shorthand']} " : null;
                            $argument = str_pad($argument, 25);
                            echo $this->color->green() . " {$argument}{$shorthand}" .
                                $this->color->normal() . $this->color->light_white() . "{$data2['_description']}" .
                                PHP_EOL . $this->color->normal();
                        }
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
        echo $this->color->light_white();
        for ($x = 0; $x < 80; $x ++) {
            echo "-";
        }
        echo $this->color->normal();
        // echo "\n";
    }
}
