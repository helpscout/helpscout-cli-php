<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('customer')
            ->setDescription('Show customer details')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Customer ID'
            )
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $helpscout = $this->app->container['helpscout'];
        $customerId = $input->getArgument('id');

        $customer = $helpscout->getCustomer($customerId);

        $customer = (array) json_decode($customer->toJSON());
        $customer['fullName'] = $customer['firstName'].' '.$customer['lastName'];

        $template = $this->app->twig->loadTemplate('customer.twig');
        $view = $template->render($customer);
        $output->write($view, false, $this->app->outputFormat);
    }
}

/* End of file CustomerCommand.php */
