<?php namespace MacchiatoPHP\Macchiato\Http;

class RedirectResponse extends Response
{
    public function __construct(string $location, int $status = self::HTTP_MOVED_PERMANENTLY, array $headers = [])
    {
        $headers['location'] = $location;
        parent::__construct('', $status, $headers);
    }
}
