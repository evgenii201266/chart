<?php namespace Ariol\Admin\Controllers;

use File;
use Validator;
use Ariol\Classes\JQFile;
use Ariol\Classes\Helper;
use Illuminate\Http\Request;

/**
 * Класс Загрузчика файлов в админке.
 * @package Ariol\Admin\Controllers
 */
class FileController extends Controller
{
    /**
     * Плагин загрузки файлов.
     */
    public function upload()
    {
        new JQFile();
    }

    /**
     * Удаление файла.
     *
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $filename = Helper::getReadableFileName($request->input('file'));

        $path[] = '/temp/' . $filename;
        $path[] = '/temp/thumbnail/' . $filename;

        foreach ($path as $p) {
            $p = public_path() . $p;

            if ($filename != null && file_exists($p)) {
                unlink($p);
            }
        }
    }

    /**
     * Загрузка изображения в текстовом редакторе.
     *
     * @param Request $request
     */
    public function summerNoteUpload(Request $request)
    {
        $image = $request->file('image');

        $validator = Validator::make($request->all(), [
            'file' => 'mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        if ($validator->fails()) {
            echo 'error';
        } else {
            $uploads_dir = public_path() . '/files/summernote/';

            $name = date('d-m-y-H-i-s') . '-' . $image->getClientOriginalName();
            $image->move($uploads_dir, $name);

            echo '/files/summernote/' . $name;
        }
    }

    /**
     * Удаление изображения в текстовом редакторе.
     *
     * @param Request $request
     */
    public function summerNoteRemove(Request $request)
    {
        $image = $request->input('image');

        if (file_exists(public_path() . $image)) {
            File::delete(public_path() . $image);
        }
    }
}
