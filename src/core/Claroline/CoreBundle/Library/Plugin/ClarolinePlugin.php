<?php

namespace Claroline\CoreBundle\Library\Plugin;

use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class ClarolinePlugin extends Bundle
{
    final public function getType()
    {
        return get_parent_class($this);
    }

    final public function getVendorName()
    {
        $namespaceParts = explode('\\', $this->getNamespace());

        return $namespaceParts[0];
    }

    final public function getBundleName()
    {
        $namespaceParts = explode('\\', $this->getNamespace());

        return $namespaceParts[1];
    }

    public function getRoutingResourcesPaths()
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = $this->getPath() . $ds . 'Resources' . $ds . 'config' . $ds . 'routing.yml';

        if (file_exists($path)) {
            return array($path);
        }

        return array();
    }

    public function getRoutingPrefix()
    {
        $vendor = $this->getVendorName();
        $prefix = $this->getBundleName();
        $pattern = '#^(.+)Bundle$#';

        if (preg_match($pattern, $prefix, $matches)) {
            $prefix = $matches[1];
        }

        $prefix = strtolower("{$vendor}_{$prefix}");

        return $prefix;
    }

    public function getNameTranslationKey()
    {
        return 'No available translated name';
    }

    public function getDescriptionTranslationKey()
    {
        return 'No available description';
    }

    public function getCustomResourcesFile()
    {
        $ds = DIRECTORY_SEPARATOR;
        $defaultFilePath = $this->getPath() . $ds . 'Resources' . $ds . 'config' . $ds . 'resources.yml';

        if (file_exists($defaultFilePath)) {
            return $defaultFilePath;
        }

        return null;
    }
}