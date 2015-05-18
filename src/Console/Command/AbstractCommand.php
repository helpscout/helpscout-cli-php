<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    public $requireAuth = false;

    public function requireAuth($bool)
    {
        $this->requireAuth = $bool;

        return $this;
    }
}

/* End of file AbstractCommand.php */
