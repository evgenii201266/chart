<?php

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Orchestra\Imagine\Facade as Imagine;

class GlideImage
{
    public function __construct()
    {
        if (! file_exists(public_path() . '/files/resized')) {
            mkdir(public_path() . '/files/resized');
        }
    }

    public static function load($path, $params)
    {
        self::modifyParams($params);

        if (is_dir(public_path() . $path) || !file_exists(public_path() . $path)) {
            if (file_exists(public_path() . '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png')) {
                return '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png';
            }

            return $path;
        }

        $nameParts = explode('/', $path);
        $name = end($nameParts);
        $name = str_replace(' ', '', $name);

        $thumbPath = public_path() . '/files/resized/' . $params['w'] . 'x' . $params['h'] . '-' . $name;
        $thumbPath = str_replace(' ', '', $thumbPath);

        if (! file_exists($thumbPath)) {
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            $size = new Box($params['w'], $params['h']);
            $thumbnail = Imagine::open(public_path() . $path)->thumbnail($size, $mode);
            $thumbnail->save($thumbPath);
        }

        return '/files/resized/' . $params['w'] . 'x' . $params['h'] . '-' . $name;
    }

    public static function resize($path, $params)
    {
        self::modifyParams($params);

        if (is_dir(public_path() . $path) || !file_exists(public_path() . $path)) {
            if (file_exists(public_path() . '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png')) {
                return '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png';
            }

            return $path;
        }

        $nameParts = explode('/', $path);
        $name = end($nameParts);
        $name = str_replace(' ', '', $name);

        $thumbPath = public_path() . '/files/resized/' . $params['w'] . 'x' . $params['h'] . '-' . $name;
        $thumbPath = str_replace(' ', '', $thumbPath);

        if (! file_exists($thumbPath)) {
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            $size = new Box($params['w'], $params['h']);
            $thumbnail = Imagine::open(public_path() . $path)->thumbnail($size, $mode);
            $thumbnail->save($thumbPath);

            $imagine = new Imagick($thumbPath);
            $imagine->thumbnailImage($params['w'], $params['h']);
            $imagine->setCompressionQuality(99);
            $imagine->writeImage(public_path() . '/files/resized/' . $params['w'] . 'x' . $params['h'] . '-' . $name);
        }

        return '/files/resized/' . $params['w'] . 'x' . $params['h'] . '-' . $name;
    }

    public static function resizeBig($path, $params)
    {
        self::modifyParams($params);

        if (is_dir(public_path() . $path) || !file_exists(public_path() . $path)) {
            if (file_exists(public_path() . '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png')) {
                return '/img/thumb' . $params['w'] . 'x' . $params['h'] . '.png';
            }

            return $path;
        }

        $nameParts = explode('/', $path);
        $name = end($nameParts);
        $name = str_replace(' ', '', $name);

        $thumbPath = public_path() . '/files/resized/bg-' . $params['w'] . 'x' . $params['h'] . '-' . $name;
        $thumbPath = str_replace(' ', '', $thumbPath);

        if (! file_exists($thumbPath)) {
            $imagine = new Imagick(public_path() . $path);
            $imagine->thumbnailImage($params['w'], $params['h'], true, true);
            $imagine->setCompressionQuality(99);
            $imagine->writeImage(public_path() . '/files/resized/bg-' . $params['w'] . 'x' . $params['h'] . '-' . $name);
        }

        return '/files/resized/bg-' . $params['w'] . 'x' . $params['h'] . '-' . $name;
    }

    public static function compact($path, $params)
    {
        if (is_dir(public_path() . $path) || !file_exists(public_path() . $path)) {
            return $path;
        }

        $nameParts = explode('/', $path);
        $name = end($nameParts);
        $name = str_replace(' ', '', $name);

        $thumbPath = public_path() . '/files/resized/rez-' . $name;
        $thumbPath = str_replace(' ', '', $thumbPath);

        if (! file_exists($thumbPath)) {
            $imagine = new Imagick(public_path() . $path);
            $imagine->resizeImage($params['w'], $params['h'], Imagick::FILTER_LANCZOS, 1);
            $imagine->setCompressionQuality(99);
            $imagine->writeImage(public_path() . '/files/resized/rez-' . $name);
        }

        return '/files/resized/rez-' . $name;
    }

    protected static function modifyParams(&$params)
    {
        if (! isset($params['w']) && isset($params['h'])) {
            $params['w'] = $params['h'];
        } elseif (isset($params['w']) && !isset($params['h'])) {
            $params['h'] = $params['w'];
        }
    }
}