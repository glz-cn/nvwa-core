<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 2016/12/9
 * Time: 下午1:58
 */

namespace NvwaCommon\Misc;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadHelper
{

    public static function snapshotLink($url, $width = null, $height = null, $q = 30)
    {
        $urlArray = explode(".", $url);
        $last = array_pop($urlArray);
        if (!is_null($width)) {
            $target[] = "w_" . $width;
        }
        if (!is_null($height)) {
            $target[] = "h_" . $height;
        }
        if ($q) {
            $target[] = "q_" . $q;
        }
        $targetSection = join(",", $target);
        if ($targetSection) {
            return env("cdnDomain") . join(".", $urlArray) . "," . $targetSection . "." . $last;
        } else {
            return env("cdnDomain") . join(".", $urlArray) . "." . $last;
        }
    }

    public function upload(Request $request, $item, $subDirectory = '')
    {

        if ($request->hasFile($item)) {
            if (!$subDirectory) {
                $subDirectory = date("Ym/dH/");
            }
            $destDirectory = $this->getUploadDirectory() . $subDirectory;
            if (!file_exists($destDirectory)) {
                mkdir($destDirectory, 0777, true);
            }
            $extension = $request->file($item)->guessExtension();
            $mime = $request->file($item)->getMimeType();
            $filename = $this->buildFileName($request, $item);
            $clientSize = $request->file($item)->getClientSize();
            $request->file($item)->move($destDirectory, $filename);
            return [
                "filename" => $filename,
                "extension" => $extension,
                "mime" => $mime,
                "filesize" => $clientSize,
                "url" => $subDirectory . $filename
            ];
        }
        Log::debug("file." . $item . " 不存在");
        return false;
    }

    private function getUploadDirectory()
    {
        return env("UPLOAD_PREFIX");
    }

    private function buildFileName(Request $request, $item)
    {
        $fi = microtime(true) . rand(99999, 999999);
        $extension = $request->file($item)->guessExtension();
        return $fi . "." . $extension;
    }
}