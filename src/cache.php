<?php

class Cache
{
    private static function identifierFromUrl($url): string
    {
        return hash('sha1', $url) . '' . hash('sha1', date('i'));
    }

    private static function fileFromIdentifier($id): string
    {
        return 'cache/' . self::identifierFromUrl($id);
    }

    public static function clear($url): void
    {
        foreach (glob("cache/*") as $cf) {
            unlink($cf);
        }
    }

    public static function exists($url): bool
    {
        $id = self::identifierFromUrl($url);
        $file = self::fileFromIdentifier($id);

        return file_exists($file);
    }

    public static function fetch($url): string
    {
        $id = self::identifierFromUrl($url);
        $file = self::fileFromIdentifier($id);

        return file_get_contents($file);
    }

    public static function create($url, $data): void
    {
        $id = self::identifierFromUrl($url);
        $file = self::fileFromIdentifier($id);

        file_put_contents($file, $data);
    }
}
