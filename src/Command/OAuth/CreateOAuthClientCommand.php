<?php

namespace App\Command\OAuth;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateOAuthClientCommand extends Command
{
    const NAME = 'oauth:create-client';

    /** @var ClientManagerInterface */
    private $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Command for creating oAuth client');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->clientManager->createClient();
        $client->setAllowedGrantTypes([OAuth2::GRANT_TYPE_USER_CREDENTIALS, OAuth2::GRANT_TYPE_REFRESH_TOKEN]);

        $this->clientManager->updateClient($client);

        $io = new SymfonyStyle($input, $output);
        $io->success(
            "\nAdded a new oAuth client\nClient Id: {$client->getPublicId()}\nClient Secret: {$client->getSecret()}"
        );
    }
}
