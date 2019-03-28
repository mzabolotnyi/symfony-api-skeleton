<?php

namespace App\Controller;

use App\Constant\ApiDoc\Tag;
use App\Constant\Serialization\Group;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * @SWG\Tag(name=CrudController::API_DOC_TAG)
 */
abstract class CrudController extends RestController
{
    const API_DOC_TAG = Tag::UNDEFINED;
    const SERIALIZE_GROUP_LIST = Group::LIST;

//    abstract protected function getEntityClass();
//    abstract protected function getEntityClass();

    /**
     * @Route("", methods={"GET"})
     *
     * @SWG\Get(summary="Get list",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK"
     *     )
     * )
     *
     * @return Response
     */
    public function getList()
    {
        return $this->response($this->getRepository()->findAll(), self::SERIALIZE_GROUP_LIST);
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(self::ENTITY_CLASS);
    }
}