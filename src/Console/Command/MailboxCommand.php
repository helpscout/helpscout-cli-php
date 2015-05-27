<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailboxCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('mailbox')
            ->setDescription('Show mailbox details')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Mailbox ID'
            )
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $helpscout = $this->app->container['helpscout'];
        $mailboxId = $input->getArgument('id');

        $mailbox = $helpscout->getMailbox($mailboxId);

        $folders = array_map(function ($folder) {
            return [
                'id' => $folder->getId(),
                'name' => $folder->getName(),
                'type' => $folder->getType(),
                'total' => $folder->getTotalCount(),
                'active' => $folder->getActiveCount(),
            ];
        }, $mailbox->getFolders());

        $mailboxData = [
            'name' => $mailbox->getName(),
            'email' => $mailbox->getEmail(),
            'folders' => $folders,
        ];

        $template = $this->app->twig->loadTemplate('mailbox.twig');
        $view = $template->render($mailboxData);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file MailboxCommand.php */
