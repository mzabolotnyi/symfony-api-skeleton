<?php

namespace App\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class RestController extends AbstractFOSRestController
{
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
    protected function response($data = null, $groups = null, int $code = null, array $headers = [])
    {
        if ($data instanceof FormInterface) {
            return $this->handleFormError($data);
        }

        if (\is_string($groups)) {
            $groups = [$groups];
        }

        $view = $this->view($data, $code, $headers);

        if (!empty($groups)) {
            $context = new Context();
            $context->setGroups($groups);
            $view->setContext($context);
        }

        return $this->handleView($view);
    }

    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }


    private function handleFormError(FormInterface $form)
    {
        $data = $this->getFormErrors($form);

        return new JsonResponse($data, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getFormErrors(FormInterface $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}