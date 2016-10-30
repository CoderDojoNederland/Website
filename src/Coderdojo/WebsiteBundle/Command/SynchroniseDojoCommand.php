<?php

namespace Coderdojo\WebsiteBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchroniseDojoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('synchronise:dojo')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();

        $dojoResponse = $this->getNetherlandsDojos($client);

        //$response = $client->send($request);
        $dojos = json_decode($dojoResponse->getBody()->getContents());
        $dojos = $dojos->Netherlands;

        $dojoZenIds = [];

        foreach($dojos as $dojo) {
            $dojoZenIds[] = $dojo->id;
            $output->writeln('Name: '.$dojo->name);
            try{
                $output->writeln('Place: '.$dojo->placeName);
            } catch (\Exception $e) {
                var_dump($dojo->place);
            }
            $output->writeln('*************************');
        }

        $dojoEvents = $this->getDojoEvents($client, $dojoZenIds);

        $dojoEvents = json_decode($dojoEvents->getBody()->getContents());

        foreach($dojoEvents as $event) {
            var_dump($event);die();
        }

        $output->writeln('Command result.');
    }

    /**
     * @param $client
     * @return ResponseInterface
     */
    private function getNetherlandsDojos(Client $client)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = '{"query":{"verified": 1, "deleted": 0, "alpha2": "NL"}}';

        $response = $client->request('POST', 'https://zen.coderdojo.com/api/2.0/dojos/by-country', [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response;
    }

    /**
     * @param $client
     * @return ResponseInterface
     */
    private function getDojoEvents(Client $client, $dojos)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $dojoList = json_encode($dojos);

        $body = '{"query":{"dojo_id": {"in$":'.$dojoList.'}}}';

        $response = $client->request('POST', 'https://zen.coderdojo.com/api/2.0/events/search', [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response;
    }

}
