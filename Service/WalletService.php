<?php

namespace CrowdValley\Bundle\ClientBundle\Service;

use CrowdValley\Bundle\AuthBundle\Service\UserService as BaseUserService;
use CrowdValley\Bundle\ClientBundle\Entity\CVResponse;
use CrowdValley\Bundle\ClientBundle\Utility\ApiManager;
use CrowdValley\Bundle\ClientBundle\Utility\DataConverter;
use CrowdValley\Bundle\ClientBundle\Entity\LoggedInUser;
use CrowdValley\Bundle\ClientBundle\Entity\Wallet;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\HttpFoundation\Session\Session;

class WalletService extends BaseUserService {

    private $apiManager;
    private $dataConverter;

    public function __construct($container, $end_point, Session $session, ApiManager $apiManager, DataConverter $dataConverter)
    {
        $this->container = $container;
        $this->end_point = $end_point;
        $this->session = $session;
        $this->network = $this->session->get('cv_network') ? $this->session->get('cv_network') : $this->container->getParameter('cv_network');
        $this->apiManager = $apiManager;
        $this->dataConverter = $dataConverter;

    }

    /**
     * Retrieve all transactions for this wallet
     * @param Wallet $wallet
     * @return CVResponse $cvResponse
     */
    public function getTransactionsForWallet(Wallet $wallet)
    {
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/wallets/'.$wallet->id.'/transactions', ['cv-auth' =>$this->container->get('user')->getToken()]);

        $responseArray = $this->apiManager->sendRequest($request);
        $cvResponse = $this->dataConverter->cvResponseForTransactions($responseArray);

        return $cvResponse;
    }
}