<?php

namespace App\Controller\File;

use App\Constant\Serialization\Group;
use App\Exception\BadRequestHttpException;
use App\Controller\RestController;
use App\Service\Media\MediaManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Entity\Media\Media;

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
     *          description="OK"
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
     * @param MediaManager $mediaManager
     * @return Response
     */
    public function upload(Request $request, MediaManager $mediaManager)
    {
        $uploadedFile = $request->files->get('file');

        if (null === $uploadedFile) {
            throw new BadRequestHttpException('error.bad_request.failed_to_upload_file');
        }

       $media = $mediaManager->createFromUploadedFile($uploadedFile);

        return $this->response($media, Group::LIST_DETAIL);
    }
}