<?php

namespace Amethyst\Http\Controllers\Admin;

use Amethyst\Core\Http\Controllers\RestManagerController;
use Amethyst\Core\Http\Controllers\Traits as RestTraits;
use Amethyst\Managers\DataBuilderManager;
use Amethyst\Managers\HttpRequesterManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpRequestersController extends RestManagerController
{
    use RestTraits\RestCommonTrait;

    /**
     * The class of the manager.
     *
     * @var string
     */
    public $class = HttpRequesterManager::class;

    /**
     * Generate.
     *
     * @param int                      $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(int $id, Request $request)
    {
        /** @var \Amethyst\Managers\HttpRequesterManager */
        $manager = $this->manager;

        /** @var \Amethyst\Models\HttpRequester */
        $email = $manager->getRepository()->findOneById($id);

        if ($email == null) {
            return $this->response('', Response::HTTP_NOT_FOUND);
        }

        $result = $manager->execute($email, (array) $request->input('data'));

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        return $this->success([]);
    }

    /**
     * Render raw template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Request $request)
    {
        /** @var \Amethyst\Managers\HttpRequesterManager */
        $manager = $this->manager;

        $dbm = (new DataBuilderManager());

        /** @var \Amethyst\Models\DataBuilder */
        $data_builder = $dbm->getRepository()->findOneById(intval($request->input('data_builder_id')));

        if ($data_builder == null) {
            return $this->error([['message' => 'invalid data_builder_id']]);
        }

        $data = (array) $request->input('data');

        $result = $dbm->build($data_builder, $data);

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        $data = array_merge($data, $result->getResource());

        if ($result->ok()) {
            $result = $manager->render(
                $data_builder,
                [
                    'url'     => strval($request->input('url')),
                    'method'  => strval($request->input('method')),
                    'headers' => strval($request->input('headers')),
                    'body'    => strval($request->input('body')),
                ],
                $data
            );
        }

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        $resource = $result->getResource();

        return $this->success(['resource' => [
            'url'     => base64_encode($resource['url']),
            'method'  => base64_encode($resource['method']),
            'headers' => base64_encode($resource['headers']),
            'body'    => base64_encode($resource['body']),
        ]]);
    }
}
