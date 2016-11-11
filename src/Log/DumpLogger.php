<?php namespace MacchiatoPHP\Macchiato\Log;

use Psr\Log\AbstractLogger;

class DumpLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        echo "<hr><pre>";
        echo "Level: $level\n";
        echo "Message: $message\n";
        echo "Context:\n";
        var_dump($context);
        echo "</pre><hr>";
    }
}
