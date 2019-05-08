<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use App\Controller\RestController;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Constant\Serialization\Group;

/**
 * @Route("<?= $route_path ?>")
 * @SWG\Tag(name="<?= $entity_class_name ?>")
 */
class <?= $class_name ?> extends RestController
{
    /**
     * @Route("", methods={"GET"})
     *
     * @SWG\Get(summary="Get list",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=<?= $entity_class_name ?>::class, groups={Group::LIST}))
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[limit]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[page]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filters[name]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *      @SWG\Parameter(
     *          name="orders[name]",
     *          in="query",
     *          type="string",
     *          required=false,
     *          enum={"ASC","DESC"},
     *      ),
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request)
    {
        return $this->response($this->getRepository()->findByParams($request->query->all()), Group::LIST);
    }

    /**
     * @Route("/{uuid}", methods={"GET"})
     *
     * @SWG\Get(summary="Get one",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=<?= $entity_class_name ?>::class, groups=Group::LIST_DETAIL)
     *     )
     * )
     *
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
    */
    public function getOne(<?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        return $this->response($<?= $entity_var_singular ?>, Group::LIST_DETAIL);
    }

    /**
     * @Route("", methods={"POST"})
     *
     * @SWG\Post(summary="Create",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=<?= $entity_class_name ?>::class, groups=Group::LIST_DETAIL)
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=<?= $form_class_name ?>::class)
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request)
    {
        return $this->update<?= $entity_class_name ?>($request, new <?= $entity_class_name ?>);
    }

    /**
     * @Route("/{uuid}", methods={"PUT"})
     *
     * @SWG\Put(summary="Update",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=<?= $entity_class_name ?>::class, groups=Group::LIST_DETAIL)
     *     ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=<?= $form_class_name ?>::class)
     *      )
     * )
     *
     * @param Request $request
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
     */
    public function put(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        return $this->update<?= $entity_class_name ?>($request, $<?= $entity_var_singular ?>);
    }

    /**
     * @Route("/{uuid}", methods={"DELETE"})
     *
     * @SWG\Delete(summary="Delete",
     *     @SWG\Response(
     *          response=Response::HTTP_NO_CONTENT,
     *          description="OK"
     *     )
     * )
     *
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
     */
    public function delete(<?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        $this->getEm()->remove($<?= $entity_var_singular ?>);
        $this->getEm()->flush();

        return $this->response();
    }

    private function update<?= $entity_class_name ?>(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->response($form);
        }

        if ($<?= $entity_var_singular ?>->isNew()) {
            $this->getEm()->persist($<?= $entity_var_singular ?>);
        }

        $this->getEm()->flush();

        return $this->response($<?= $entity_var_singular ?>, Group::LIST_DETAIL);
    }

    /**
<?php if (isset($repository_class_name)): ?>
     * @return <?= $repository_class_name ?>|ObjectRepository
<?php else: ?>
     * @return ObjectRepository
<?php endif ?>
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository(<?= $entity_class_name ?>::class);
    }
}