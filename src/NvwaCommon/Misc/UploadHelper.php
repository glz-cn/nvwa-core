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
    public function pasteUpload(Request $request, $item, $subDirectory = '')
    {
        $file =$request->get('file');
        $seg = explode(";",$file);
        if(sizeof($seg)!=2){
            return resError(400,"wrong paramater" );
        }
        $segments = explode(",",$seg[1]);
        if(sizeof($segments)!=2){
            return resError(400,"wrong paramter");
        }
        $data = $segments[1];
        $encoding = $segments[0];

        $header = explode(":",$seg[0]);
        if(sizeof($header)!=2){
            return resError(400,"wrong paramter");
        }

        $mime = explode("/",$header[1]);
        if(sizeof($header)!=2){
            return resError(400,"wrong paramter");
        }
        $extName =  strtolower($mime[1]);
        if(!in_array($extName,array("jpg","bmp","gif","png"))) {
            return resError(403,"bad extension name of the file");
        }
        try{
            $real_data = base64_decode($data);
        }catch(Exception $e){
            return resError(402,"parse data failed");
        }
        $y= date("Ym");
        if (!$subDirectory) {
            $subDirectory = date("Ym/dH/");
        }
        $destDirectory = $this->getUploadDirectory() . $subDirectory;
        if (!file_exists($destDirectory)) {
            mkdir($destDirectory, 0777, true);
        }
        $extension = $extName;
        $mime = $header[1];
        $filename = $this->buildPasteFileName($request, $extension);
        file_put_contents($destDirectory.$filename,$real_data);
        $clientSize = filesize($destDirectory.$filename);
        return [
            "filename" => $filename,
            "extension" => $extension,
            "mime" => $mime,
            "filesize" => $clientSize,
            "url" => $subDirectory . $filename
        ];
    }
    function resError($code,$msg){
        return ['code'=>$code,'msg'=>$msg];
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
    private function buildPasteFileName(Request $request, $extension)
    {
        $fi = microtime(true) . rand(99999, 999999);
        return $fi . "." . $extension;
    }
}