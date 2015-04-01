<?php

/* Extended environmnent for the drupal version
*
*/

class TFD_Environment extends Twig_Environment
{

    protected $templateClassPrefix = '__TFDTemplate_';
    protected $fileExtension = 'tpl.twig';
    protected $autoRender = false;

    public function __construct(Twig_LoaderInterface $loader = null, $options = array())
    {
        $this->fileExtension = twig_extension();
        $options = array_merge(array(
            'autorender' => true,
        ), $options);
        // Auto render means, overrule default class
        if ($options['autorender']) {
            $this->autoRender = true;
        }
        parent::__construct($loader, $options);
    }

    private function generateCacheKeyByName($name)
    {
        return $name = preg_replace('/\.' . $this->fileExtension . '$/', '', $this->loader->getCacheKey($name));
    }

    public function isAutoRender()
    {
        return $this->autoRender;
    }

    /**
     * returns the name of the class to be created
     * which is also the name of the cached instance
     *
     * @param <string> $name of template
     * @return <string>
     */
    public function getTemplateClass($name, $index = null)
    {
        return str_replace(array('-', '.', '/'), "_", $this->generateCacheKeyByName($name));
    }

    public function loadTemplate($name, $index = null)
    {

        if (substr_count($name, '::') == 1) {
            $paths = twig_get_discovered_templates(); // Very expensive call
            $name = $paths[$name];
        }

        return parent::loadTemplate($name, $index);
    }

    public function getCacheFilename($name)
    {
        if ($cache = $this->getCache()) {
            $name = $this->generateCacheKeyByName($name);
            $name .= '.php';
            $dir = $cache . '/' . dirname($name);
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new Exception("Cache directory $cache is not deep writable?");
                }
            }
            return $cache . '/' . $name;
        }
    }


    public function flushCompilerCache()
    {
        if (is_dir($this->getCache())) {
            $iterator = new RecursiveDirectoryIterator($this->getCache());
            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                if ($file->isDir()) {
                    @rmdir($file->getPathname());
                } else {
                    @unlink($file->getPathname());
                }
            }
            @rmdir($this->getCache());
        }
    }


    protected function writeCacheFile($file, $content)
    {
        try {
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            $tmpFile = tempnam(dirname($file), basename($file));
            if (false !== @file_put_contents($tmpFile, $content)) {
                // rename does not work on Win32 before 5.2.6
                if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile))) {
                    @chmod($file, 0644);
                    // Just in case apc.stat = 0, force the cache file into APC cache!
                }
            }
        } catch (Exception $exception) {
            throw new Twig_Error_Runtime(sprintf('Failed to write cache file "%s".', $file));
        }
    }
}