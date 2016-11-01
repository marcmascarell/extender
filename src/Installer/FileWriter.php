<?php

namespace Mascame\Extender\Installer;

class FileWriter implements FileWriterInterface
{
    /**
     * @param $path
     * @param $contents
     * @param bool|false $lock
     * @return int
     */
    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }
}
