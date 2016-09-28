<?php

namespace App\Http\Controllers\Attachment;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Input;
use App\Attachment;
use App\User;
use App\Role;

class AttachmentController extends Controller
{
    public function upload()
    {
        $ssid = Input::get('ssid');

        $user_id = Crypt::decrypt($ssid);
        if ( ! empty($user_id)) {
            $user = User::find($user_id);
        }

        if (empty($user_id) or empty($user)) {
            \App::abort(403, 'Auth failed!');
        }

        $timestamp = Input::get('timestamp');
        $token = Input::get('token');

        if (md5('tpc_salt' . $timestamp) != $token) {
            return response()->json(['state' => 'error', 'msg' => '验证失败']);
        }

        $filetype = Input::get('filetype');
        $filesize = intval(Input::get('filesize'));
        if ($filesize <= 0) {
            $filesize = 1048576;
        }

        $time = time();
        /*$oss = App::make('OSS');
        $bucket = config('oss.buckets.brand');
        */

        if ( ! \Request::hasFile('upfile')) {
            return response()->json(['state'=>'error', 'msg'=>'没有文件被上传']);
        }

        $file = \Request::file('upfile');

        if ( ! $file->isValid()) {
            return response()->json(['state'=>'error', 'msg'=>'不合法的文件']);
        }
        if ($file->getSize() > $filesize * 10) {
            return response()->json(['state'=>'error', 'msg'=>'文件大小超出10MB']);
        }
        /*$mime_types = [
            'application/msword', 'application/vnd.oasis.opendocument.text',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/rtf', 'application/pdf', 'application/kswps'
        ];*/
        $mime_types = config('addition.templates.file.type.'.$filetype.'.mime_type');
        if ( ! in_array($file->getMimeType(), $mime_types)) {
            return response()->json(['state'=>'error', 'msg'=>'错误的文件类型']);
        }

        //$file_ext = explode('/', $file->getMimeType())[1];
        $file_origina_name = $file->getClientOriginalName();

        $file_dir = date('Y/m', $time);
        $file_name = $time.'-'.$user->id.'.'.$file->getClientOriginalExtension();

        $file_path = 'attachment/' . $file_dir;

        $dist_path = storage_path($file_path) . '/' . $file_name;


        if ($file->move(storage_path($file_path), $file_name)) {
            $md5 = md5_file($dist_path);
            $sha1 = sha1_file($dist_path);
            $exists_attachment = Attachment::where('md5sum', $md5)->where('sha1sum', $sha1)->first();
            if ( ! empty($exists_attachment)) {
                if ($exists_attachment->user_id == $user->id) {
                    $attachment = $exists_attachment;
                    $attachment->updated_at = date('Y-m-d H:i:s');
                } else {
                    $attachment = Attachment::create([
                        'user_id'  => $user->id,
                        'filename' => $file_origina_name,
                        'path'     => $exists_attachment->path,
                        'md5sum'      => $md5,
                        'sha1sum'     => $sha1
                    ]);
                }
                $file_origina_name = $attachment->filename;
                unlink($dist_path);
            } else {
                $attachment = Attachment::create([
                    'user_id'  => $user->id,
                    'filename' => $file_origina_name,
                    'path'     => $file_path. '/' .$file_name,
                    'md5sum'      => $md5,
                    'sha1sum'     => $sha1
                ]);
            }
        }

        if ( ! empty($attachment)) {
            if ($attachment->save()) {
                return response()->json(['state' => 'success', 'filename' => $file_origina_name, 'attachment_id'=>$attachment->id]);
            }
        }

        /*$object = $file->getRealPath();

        $response = $oss->create_object_dir($bucket,$file_dir);
        if ($response->status == 200) {
            $response = $oss->upload_file_by_file($bucket, $file_path, $object, array('Content-Type' => $file->getMimeType()));
            if ($response->status == 200) {
                return response()->json(array('state' => 'success', 'msg' => $file_path));
            }
        }
        */
        //\Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFilesize();

        return response()->json(array('state' => 'error', 'msg' => '上传发生未知错误'));
    }

    public function download(Attachment $attachment)
    {
        return response()->download(storage_path($attachment->path), $attachment->filename);
    }
}
