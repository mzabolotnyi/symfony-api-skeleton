<?php

namespace App\Controller;

use App\Constant\Serialization\Group;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends RestController
{
    const SERIALIZE_GROUP_LIST = Group::LIST;
    const SERIALIZE_GROUP_DETAIL = Group::LIST_DETAIL;

    abstract protected function getEntityClass();

    abstract protected function getFormClass();

    /**
     * Get collection of entities
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getList(Request $request)
    {
        return $this->response($this->getRepository()->findAll(), self::SERIALIZE_GROUP_LIST);
    }

    /**
     * Get single entity
     *
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getOne($entity)
    {
        return $this->response($entity, self::SERIALIZE_GROUP_DETAIL);
    }

    /**
     * Create entity
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(Request $request)
    {
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass;

        return $this->updateEntity($request, $entity);
    }

    /**
     * Update entity
     *
     * @param Request $request
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function put(Request $request, $entity)
    {
        return $this->updateEntity($request, $entity);
    }

    /**
     * Remove entity
     *
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($entity)
    {
        $this->getEm()->remove($entity);
        $this->getEm()->flush();

        return $this->response();
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository($this->getEntityClass());
    }

    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function updateEntity(Request $request, $entity)
    {
        $form = $this->createForm($this->getFormClass(), $entity);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        if (null === $entity->getId()) {
            $this->getEm()->persist($entity);
        }

        $this->getEm()->flush();

        return $this->response($entity, self::SERIALIZE_GROUP_DETAIL);
    }
}