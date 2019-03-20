<?php

namespace App\Controller;

use App\Constant\Serialization\Group;
use App\Service\Response\ErrorResponseHandler;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class RestController extends AbstractFOSRestController
{
    /** @var ErrorResponseHandler */
    private $errorResponseHandler;

    public function __construct(ErrorResponseHandler $errorResponseHandler)
    {
        $this->errorResponseHandler = $errorResponseHandler;
    }

    /**
     * Generate response
     *
     * Pass any data you want to return in response. If data is null and code not set method will generate empty response with 204 http code
     * If you use serialization groups you can pass array of needed groups or name of single group
     * Also you can pass custom headers that will be added to response
     *
     * @param $data
     * @param array|null $groups
     * @param array|string $headers
     * @param int $code
     * @return Response
     */
    public function response($data = null, $groups = null, int $code = null, array $headers = [])
    {
        if ($data instanceof FormInterface) {
            return $this->errorResponseHandler->handleFormError($data);
        }

        if (\is_string($groups)) {
            $groups = [$groups];
        }

        if ($groups === null || !\in_array(Group::DEFAULT, $groups)) {
            $groups[] = Group::DEFAULT;
        }

        $view = $this->view($data, $code, $headers);

        if (!empty($groups)) {
            $context = new Context();
            $context->setGroups($groups);
            $view->setContext($context);
        }

        return $this->handleView($view);
    }
}