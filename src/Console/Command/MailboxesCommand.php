<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailboxesCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('mailboxes')
            ->setDescription('List all mailboxes')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $helpscout = $this->app->container['helpscout'];
        $mailboxes = $helpscout->getMailboxes();

        // TODO: Pagination to collect all mailboxes
        $mailboxes = $this->mailboxesToArray($mailboxes);

        $template = $this->app->twig->loadTemplate('mailboxes.twig');

        $view = $template->render([
            'mailboxes' => $mailboxes
        ]);
        $output->write($view, false, $this->app->outputFormat);
    }

    private function mailboxesToArray($mailboxes)
    {
        return array_map(function ($mailbox) {
            return [
                'id'    => $mailbox->getId(),
                'name'  => $mailbox->getName(),
                'email' => $mailbox->getEmail()
            ];
        }, $mailboxes->getItems());
    }
}

/* End of file MailboxesCommand.php */
