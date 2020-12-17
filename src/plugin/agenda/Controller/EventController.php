<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\AgendaBundle\Controller;

use Claroline\AgendaBundle\Entity\Event;
use Claroline\AgendaBundle\Manager\AgendaManager;
use Claroline\AppBundle\Controller\AbstractCrudController;
use Claroline\CoreBundle\Entity\File\PublicFile;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Claroline\CoreBundle\Library\Utilities\FileUtilities;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/event")
 */
class EventController extends AbstractCrudController
{
    /** @var AuthorizationCheckerInterface */
    private $authorization;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var FileUtilities */
    private $fileUtils;
    /** @var AgendaManager */
    private $manager;

    public function __construct(
        AuthorizationCheckerInterface $authorization,
        TokenStorageInterface $tokenStorage,
        FileUtilities $fileUtils,
        AgendaManager $manager
    ) {
        $this->authorization = $authorization;
        $this->tokenStorage = $tokenStorage;
        $this->fileUtils = $fileUtils;
        $this->manager = $manager;
    }

    public function getClass()
    {
        return Event::class;
    }

    public function getName()
    {
        return 'event';
    }

    /**
     * @param string $class
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, $class)
    {
        $query = $request->query->all();
        $hiddenFilters = isset($query['hiddenFilters']) ? $query['hiddenFilters'] : [];
        $query['hiddenFilters'] = array_merge($hiddenFilters, $this->getDefaultHiddenFilters());

        // get start & end date and add them to the hidden filters list
        $query['hiddenFilters']['inRange'] = [$query['start'] ?? null, $query['end'] ?? null];

        if (!isset($query['filters']['workspaces'])) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ('anon.' !== $user) {
                $query['hiddenFilters']['user'] = $user->getUuid();
            } else {
                $query['hiddenFilters']['anonymous'] = true;
            }
        }

        $data = $this->finder->search(
            $class,
            $query,
            $this->options['list']
        );

        return new JsonResponse($data);
    }

    /**
     * Marks a list of tasks as done.
     *
     * @Route("/done", name="apiv2_task_mark_done", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function markDoneAction(Request $request)
    {
        /** @var Event[] $tasks */
        $tasks = $this->decodeIdsString($request, Event::class);
        foreach ($tasks as $task) {
            if ($this->checkPermission($task) && $task->isTask() && !$task->isTaskDone()) {
                $task->setIsTaskDone(true);
                $this->om->persist($task);
            }
        }

        $this->om->flush();

        return new JsonResponse(array_map(function (Event $task) {
            return $this->serializer->serialize($task);
        }, $tasks));
    }

    /**
     * Marks a list of tasks as to do.
     *
     * @Route("/todo", name="apiv2_task_mark_todo", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function markTodoAction(Request $request)
    {
        /** @var Event[] $tasks */
        $tasks = $this->decodeIdsString($request, Event::class);
        foreach ($tasks as $task) {
            if ($this->checkPermission($task) && $task->isTask() && $task->isTaskDone()) {
                $task->setIsTaskDone(false);
                $this->om->persist($task);
            }
        }

        $this->om->flush();

        return new JsonResponse(array_map(function (Event $task) {
            return $this->serializer->serialize($task);
        }, $tasks));
    }

    private function checkPermission(Event $event)
    {
        if (false === $event->isEditable()) {
            return false;
        }

        if ($event->getWorkspace()) {
            if (!$this->authorization->isGranted(['agenda', 'edit'], $event->getWorkspace())) {
                return false;
            }

            return true;
        }

        if ($this->tokenStorage->getToken()->getUser() !== $event->getUser()) {
            return false;
        }

        return true;
    }
}
