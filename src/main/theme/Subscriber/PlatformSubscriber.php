<?php

namespace Claroline\ThemeBundle\Subscriber;

use Claroline\AppBundle\API\SerializerProvider;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Event\GenericDataEvent;
use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Claroline\ThemeBundle\Entity\ColorCollection;
use Claroline\ThemeBundle\Manager\IconSetManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlatformSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly PlatformConfigurationHandler $config,
        private readonly IconSetManager $iconManager,
        private readonly ObjectManager $objectManager,
        private readonly SerializerProvider $serializer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'claroline_populate_client_config' => 'onConfig',
        ];
    }

    public function onConfig(GenericDataEvent $event): void
    {
        $colorCharts = $this->objectManager->getRepository(ColorCollection::class)->findAll();
        $chartsData = [];

        foreach ($colorCharts as $chart) {
            $chartsData[] = $this->serializer->serialize($chart);
        }

        $event->setResponse([
            'theme' => [
                'name' => strtolower($this->config->getParameter('theme')),
                'icons' => $this->iconManager->getCurrentSet(),
            ],
            'colorChart' => $chartsData,
        ]);
    }
}
