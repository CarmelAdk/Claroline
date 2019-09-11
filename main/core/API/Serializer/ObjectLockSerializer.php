<?php

namespace Claroline\CoreBundle\API\Serializer;

use Claroline\AppBundle\API\Options;
use Claroline\CoreBundle\API\Serializer\User\UserSerializer;
use Claroline\CoreBundle\Entity\ObjectLock;
use Claroline\CoreBundle\Library\Normalizer\DateNormalizer;

class ObjectLockSerializer
{
    public function __construct(
        UserSerializer $userSerializer
    ) {
        $this->userSerializer = $userSerializer;
    }

    public function getClass()
    {
        return ObjectLock::class;
    }

    public function serialize(ObjectLock $lock): array
    {
        return [
          'user' => $this->userSerializer->serialize($lock->getUser(), [Options::SERIALIZE_MINIMAL]),
          'value' => $lock->isLocked(),
          'updated' => DateNormalizer::normalize($lock->getLastModification()),
          'className' => $lock->getObjectClass(),
          'id' => $lock->getObjectUuid(),
        ];
    }
}
