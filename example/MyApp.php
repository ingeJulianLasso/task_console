<?php
use TaskConsole\Handler;

require '../vendor/autoload.php';

class MyApp extends Handler
{

    public function __construct($title, $version, $task_path)
    {
        $this->registerCommands();
        parent::__construct($title, $version, $task_path);
    }

    private function registerCommands(): void
    {
        $this->registerCommand('generate_mapping', 'Mostrara la ayuda de la consola.', 'gm');
        $this->registerArgument('generate_mapping', 'version1', 'Muestra la versión.', 'v1');
        $this->registerArgument('generate_mapping', 'version2', 'Muestra la versión.', 'v2');
        
        $this->registerCommand('help2', 'Mostrara la ayuda de la consola.', 'h2');
        $this->registerArgument('help2', 'version', 'Muestra la versión.', 'v3');
        
        $this->registerCommand('help3', 'Mostrara la ayuda de la consola.', 'h3');
        
        $this->registerCommand('help4', 'Mostrara la ayuda de la consola.', 'h4');
        $this->registerArgument('help4', 'version', 'Muestra la versión.', 'v4');
        
        $this->registerCommand('help5', 'Mostrara la ayuda de la consola.', 'h5');
    }
}