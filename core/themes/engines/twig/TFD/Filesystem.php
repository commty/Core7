<?php

/**
 * Loads template from the filesystem.
 */
class TFD_Loader_Filesystem extends Twig_Loader_Filesystem
{
    protected $resolverCache;

    public function __construct()
    {
        parent::__construct(array());
        $this->resolverCache = array();
    }


    public function getSource($filename)
    {
        return file_get_contents($this->getCacheKey($filename));
    }


    public function findTemplate($name)
    {
        $this->validateName($name);
        if (!isset($this->resolverCache[$name])) {
            $found = false;
            if (is_readable($name)) {
                $this->resolverCache[$name] = $name;
                $found = true;
            } else {
                $paths = twig_get_discovered_templates();

                if (array_key_exists($name, $paths)) {
                    $completeName = $paths[$name];
                    if (is_readable($completeName)) {
                        $this->resolverCache[$name] = $completeName;
                        $found = true;
                    }
                }
            }
            if (!$found) throw new Twig_Error_Loader(sprintf('Could not find a cache key for template "%s"', $name));
        }
        return $this->resolverCache[$name];
    }

}

