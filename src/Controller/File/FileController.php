<?php

namespace App\Controller\File;

use App\Exception\BadRequestHttpException;
use App\Controller\RestController;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Constant\Serialization\Group;

/**
 * @Route("file")
 * @SWG\Tag(name="File")
 */
class FileController extends RestController
{
    /**
     * @Route("", methods={"POST"})
     *
     * @SWG\Post(summary="Create",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=File::class, groups=Group::LIST_DETAIL)
     *     ),
     *     @SWG\Parameter(
     *          name="file",
     *          in="formData",
     *          type="file",
     *          required=true,
     *          description="Uploaded file"
     *     )
     * )
     *
     * @param Request $request
     * @param UploadableManager $uploadableManager
     * @return Response
     */
    public function upload(Request $request, UploadableManager $uploadableManager)
    {
        $uploadedFile = $request->files->get('file');

        if (null === $uploadedFile) {
            throw new BadRequestHttpException('error.bad_request.failed_to_upload_file');
        }

//        $file = new File();
//        $uploadableManager->markEntityToUpload($file, $uploadedFile);
//
//        $this->getEm()->persist($file);
//        $this->getEm()->flush();
//
//        return $this->response($file, Group::LIST_DETAIL);
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
     * @param File $file
     * @return Response
     */
//    public function delete(File $file)
//    {
//        $this->getEm()->remove($file);
//        $this->getEm()->flush();
//
//        return $this->response();
//    }
}