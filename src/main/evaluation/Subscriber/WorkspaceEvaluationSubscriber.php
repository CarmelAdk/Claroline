<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\EvaluationBundle\Subscriber;

use Claroline\AppBundle\API\Crud;
use Claroline\AppBundle\Event\Crud\DeleteEvent;
use Claroline\AppBundle\Event\Crud\UpdateEvent;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Component\Context\WorkspaceContext;
use Claroline\CoreBundle\Entity\Resource\ResourceNode;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Claroline\CoreBundle\Event\CatalogEvents\ContextEvents;
use Claroline\CoreBundle\Event\CatalogEvents\SecurityEvents;
use Claroline\CoreBundle\Event\Context\OpenContextEvent;
use Claroline\CoreBundle\Event\Security\AddRoleEvent;
use Claroline\CoreBundle\Repository\WorkspaceRepository;
use Claroline\EvaluationBundle\Event\EvaluationEvents;
use Claroline\EvaluationBundle\Event\ResourceEvaluationEvent;
use Claroline\EvaluationBundle\Library\EvaluationStatus;
use Claroline\EvaluationBundle\Manager\WorkspaceEvaluationManager;
use Claroline\EvaluationBundle\Messenger\Message\InitializeWorkspaceEvaluations;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Updates the WorkspaceEvaluation in response to application events.
 */
class WorkspaceEvaluationSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private MessageBusInterface $messageBus;
    private WorkspaceEvaluationManager $manager;
    private WorkspaceRepository $workspaceRepo;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        MessageBusInterface $messageBus,
        ObjectManager $om,
        WorkspaceEvaluationManager $manager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->messageBus = $messageBus;
        $this->manager = $manager;

        $this->workspaceRepo = $om->getRepository(Workspace::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContextEvents::OPEN => 'onOpen',
            SecurityEvents::ADD_ROLE => 'onAddRole',
            EvaluationEvents::RESOURCE_EVALUATION => 'onResourceEvaluate',
            Crud::getEventName('update', 'post', ResourceNode::class) => 'onResourcePublicationChange',
            Crud::getEventName('delete', 'post', ResourceNode::class) => 'onResourceDelete',
        ];
    }

    /**
     * Updates the workspace evaluation status to "opened".
     */
    public function onOpen(OpenContextEvent $event): void
    {
        if (WorkspaceContext::getName() !== $event->getContextType()) {
            return;
        }

        // Update current user evaluation
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof User) {
            $this->manager->updateUserEvaluation(
                $event->getContextSubject(),
                $user,
                ['status' => EvaluationStatus::OPENED]
            );
        }
    }

    /**
     * Initializes evaluations for newly registered users.
     */
    public function onAddRole(AddRoleEvent $event): void
    {
        $role = $event->getRole();

        // init evaluation for all the workspaces accessible by the role
        // this is not required by the code, but is a feature for managers to see users in evaluation tool/exports
        // event if they have not opened the workspace yet.
        $workspaces = $this->workspaceRepo->findByRoles([$role->getName()]);
        foreach ($workspaces as $workspace) {
            $this->messageBus->dispatch(
                new InitializeWorkspaceEvaluations($workspace->getId(), array_map(function (User $user) {
                    return $user->getId();
                }, $event->getUsers()))
            );
        }
    }

    /**
     * Updates WorkspaceEvaluation each time a user is evaluated for a Resource.
     */
    public function onResourceEvaluate(ResourceEvaluationEvent $event): void
    {
        $resourceUserEvaluation = $event->getEvaluation();
        $resourceNode = $resourceUserEvaluation->getResourceNode();
        $workspace = $resourceNode->getWorkspace();
        $user = $resourceUserEvaluation->getUser();

        $this->manager->computeEvaluation($workspace, $user);
    }

    /**
     * Recomputes WorkspaceEvaluations when a resource is deleted.
     */
    public function onResourceDelete(DeleteEvent $event): void
    {
        /** @var ResourceNode $resourceNode */
        $resourceNode = $event->getObject();

        $this->manager->recompute($resourceNode->getWorkspace());
    }

    public function onResourcePublicationChange(UpdateEvent $event): void
    {
        /** @var ResourceNode $resourceNode */
        $resourceNode = $event->getObject();
        $oldData = $event->getOldData();

        if ($resourceNode->isRequired() && !empty($oldData['meta']) && ($oldData['meta']['published'] !== $resourceNode->isPublished())) {
            $this->manager->recompute($resourceNode->getWorkspace());
        }
    }
}
