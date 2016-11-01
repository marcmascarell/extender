<?php

namespace Mascame\Extender\Installer;

interface FileWriterInterface
{
    /**
     * @param $path
     * @param $contents
     * @param bool|false $lock
     * @return mixed
     */
    public function put($path, $contents, $lock = false);
}
